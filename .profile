# Setup some basic things for easier usage in SSH when necessary.

export PATH="$HOME/bin:$PATH"

function magentodb {
    `php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); $db = $config["db"]["connection"]["default"]; echo "mysql -h$db[host] -u$db[username] $db[dbname]"; if ($db["password"]) { echo " -p" . $db["password"]; }'` "$@"
}

function magentostat_check_deploy {
    # In case the user is wondering why the site isn't loading.
    if [ `php $HOME/bin/magento maintenance:status | grep -q 'not active'` ]; then
        echo "🚑 Maintenance mode active."
    fi
    if [ `php $HOME/bin/magento app:config:status 2>/dev/null | grep -q 'are up to date'` ]; then
        echo "⚙️ Config files not up to date."
    fi
    if [ `php $HOME/bin/magento setup:db:status | grep -q 'All modules are up to date'` ]; then
        echo "💾 Database needs update (hopefully upgrade, possibly downgrade.)"
    fi
}

function magentostat_pro_check_dbrepl {
    DBSTATUS="`magentodb -B -e "SHOW GLOBAL STATUS LIKE 'wsrep_local_state_comment'" | grep wsrep | gawk '{ print $2; }'`"
    DBNODES="`magentodb -B -e "SHOW GLOBAL STATUS LIKE 'wsrep_cluster_size'" | grep wsrep | gawk '{ print $2; }'`"
    # This should tell us if the database is out of sync, maybe a node is broken.
    if [ "$DBSTATUS" != "Synced" ]; then
        echo "💔 Database sync status: $DBSTATUS"
    fi
    # And this should tell us if a node is offline.
    if [ "$DBNODES" != "3" ]; then
        echo "👻 Database nodes missing?  Current: $DBNODES"
    fi
}

function magentostat_check_elasticsearch {
    SEARCH_CONNECTION="`php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); $search = &$config["system"]["default"]["catalog"]["search"]; if (empty($search) || $search["engine"] == "mysql") { echo "mysql"; } else { echo $search[$search["engine"] . "_server_hostname"] . ":" . $search[$search["engine"] . "_server_port"]; }'`"
    if [ "$SEARCH_CONNECTION" = "mysql" ]; then
        echo "Catalog filter backend: MySQL"
    else
        # We're assuming not Solr here.
        echo -n "Catalog filter backend: Elasticsearch "
        curl -s "http://$SEARCH_CONNECTION" | grep number | gawk -F'"' '{ print $4; }'
    fi
}

function magenostat_check_dbconfig {
    # Magento seems to still default some URLs to http://, make sure we haven't left any.
    URLS="`magentodb -B -e "SELECT value FROM core_config_data WHERE path LIKE 'web/%/base_url';" | grep -v value | sort -u`"
    if [[ "$URLS" == *http:* ]]; then
        echo "🔓 Non-SSL URLs in configuration.";
    fi
}

function magentostat_check_fastly {
    CACHING="`magentodb -B -e "SELECT value FROM core_config_data WHERE path = 'system/full_page_cache/caching_application'" | grep -v value`"
    if [ "$CACHING" != "fastly" ]; then
        echo "⏳ Caching backend not Fastly ($CACHING)"
    else
        # Let's also check each URL has a valid SSL cert.  This step is a bit slow but it's a good check.
        # This specifically checks www as well for pre-production validation.
        for url in $URLS; do
            URL_HOSTNAME="`echo $url | gawk -F'/' '{ print $3; }'`"
            echo -n "SSL for $URL_HOSTNAME: "
            timeout 1s curl -XHEAD -sSI --resolve $URL_HOSTNAME:443:443:151.101.193.124 https://$URL_HOSTNAME/ | grep HTTP
            URL_WWW="`echo $URL_HOSTNAME | sed -E 's/^(mcprod|mcstaging|prod|staging)\./www./'`"
            if [ "$URL_WWW" != "$URL_HOSTNAME" ]; then
                echo -n "SSL for $URL_WWW: "
                timeout 1s curl -XHEAD -sSI --resolve $URL_WWW:443:443:151.101.193.124 https://$URL_WWW/ | grep HTTP
            fi
        done
    fi
}

function magentostat_check_var_report {
    # If var/report/ gets too big, it will cause deploys to hang during setup:upgrade.
    # Big enough, and it'll even cause an error and setup:upgrad will just fail.
    # This size is approximate, but it'll help us find the issue.
    REPORTS_SIZE="`(timeout 1s ls -1f var/report || true) | wc -l`"
    if [ "$REPORTS_SIZE" -gt 5 ]; then
        echo "🔕 Reports (low estimate, may slow deploy): $REPORTS_SIZE"
    fi
}

function magentostat_check_fpc {
    find vendor/ app/ -name default.xml -o -name default_head_blocks.xml -o -name catalog_product_view.xml -o -name cms_index_index.xml -o -name cms_noroute_index.xml -o -name cms_page_view.xml -o -name catalog_category_view.xml -o -name catalog_category_view_type_default.xml -o -name catalog_category_view_type_layered.xml -o -name catalog_category_view_type_default_without_children.xml | grep frontend/layout | xargs grep --color=auto -E 'cacheable.*false'
}

function magentostat_check_cron {
    # On Cloud, this happens most commonly when a deploy fails and someone manually fixes setup:upgrade.
    CRONSTATUS="`php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); if (empty($config["cron"]["enabled"])) { echo "Disabled"; }'`"
    if [ "$CRONSTATUS" != "" ]; then
        echo "⏲️ Cron is currently $CRONSTATUS" | grep --color=auto Disabled
    fi

    ps axo etime,args | grep -v grep | grep --color=auto -e cron:run -e indexer

    php $HOME/bin/magento indexer:status
    magentodb -e "SELECT status AS cron_status, MAX(executed_at), MAX(scheduled_at), SUBSTRING(GROUP_CONCAT(DISTINCT job_code), 1, 60) AS jobs_truncated FROM cron_schedule GROUP BY status;"
}

function magentostat_check_versions {
    # Just for a quick display of what the versions are at.
    composer show magento/* --working-dir=$HOME 2>/dev/null | grep -e magento/magento-cloud-metapackage -e magento/ece-tools --color=auto
    composer show somethingdigital/magento2-theme-* --working-dir=$HOME 2>/dev/null | grep somethingdigital/magento2-theme-bryantpark --color=auto
}

function magentostat_check_deploylog {
    # There may be useful errors in this log, but only display if there are any.
    tail var/log/cloud.log | grep -q var/.deploy_is_failed && tail var/log/cloud.log | grep --color=auto -C99 -e failed -e CRITICAL -e 'returned code 1'
}

function magentostat {
    magentostat_check_deploy

    if [ "$MAGENTO_CLOUD_BRANCH" = "production" -o "$MAGENTO_CLOUD_BRANCH" = "staging" ]; then
        magentostat_pro_check_dbrepl
        df --output -h /data/* /
        tail -n1 /var/log/platform/$LOGNAME/php5-fpm.log /var/log/platform/$LOGNAME/error.log
    else
        df --output -h /mnt /
        tail -n1 /var/log/nginx/error.log /var/log/error.log
    fi

    magentostat_check_elasticsearch
    magentostat_check_fastly
    magentostat_check_fpc
    magentostat_check_var_report
    magentostat_check_cron
    magentostat_check_versions
    magentostat_check_deploylog
}
