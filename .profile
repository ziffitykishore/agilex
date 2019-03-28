# Setup some basic things for easier usage in SSH when necessary.

export PATH="$HOME/bin:$PATH"

function magentodb {
    `php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); $db = $config["db"]["connection"]["default"]; echo "mysql -h$db[host] -u$db[username] $db[dbname]"; if ($db["password"]) { echo " -p" . $db["password"]; }'` "$@"
}

function magentostat {
    if [ `php $HOME/bin/magento maintenance:status | grep -q 'not active'` ]; then
        echo "🚑 Maintenance mode active."
    fi
    if [ `php $HOME/bin/magento app:config:status 2>/dev/null | grep -q 'are up to date'` ]; then
        echo "⚙️ Config files not up to date."
    fi
    if [ `php $HOME/bin/magento setup:db:status | grep -q 'All modules are up to date'` ]; then
        echo "💾 Database needs update."
    fi

    if [ "$MAGENTO_CLOUD_BRANCH" = "production" -o "$MAGENTO_CLOUD_BRANCH" = "staging" ]; then
        DBSTATUS="`magentodb -B -e "SHOW GLOBAL STATUS LIKE 'wsrep_local_state_comment'" | grep wsrep | gawk '{ print $2; }'`"
        DBNODES="`magentodb -B -e "SHOW GLOBAL STATUS LIKE 'wsrep_cluster_size'" | grep wsrep | gawk '{ print $2; }'`"
        if [ "$DBSTATUS" != "Synced" ]; then
            echo "💔 Database sync status: $DBSTATUS"
        fi
        if [ "$DBNODES" != "3" ]; then
            echo "👻 Database nodes missing?  Current: $DBNODES"
        fi
        df --output -h /data/* /

        tail -n1 /var/log/platform/$LOGNAME/php5-fpm.log /var/log/platform/$LOGNAME/error.log
    else
        df --output -h /mnt /
    fi

    SEARCH_CONNECTION="`php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); $search = &$config["system"]["default"]["catalog"]["search"]; if (empty($search) || $search["engine"] == "mysql") { echo "mysql"; } else { echo $search[$search["engine"] . "_server_hostname"] . ":" . $search[$search["engine"] . "_server_port"]; }'`"
    if [ "$SEARCH_CONNECTION" = "mysql" ]; then
        echo "Catalog filter backend: MySQL"
    else
        echo -n "Catalog filter backend: Elasticsearch "
        curl -s "http://$SEARCH_CONNECTION" | grep number | gawk -F'"' '{ print $4; }'
    fi

    URLS="`magentodb -B -e "SELECT value FROM core_config_data WHERE path LIKE 'web/%/base_url';" | grep -v value | sort -u`"
    if [[ "$URLS" == *http:* ]]; then
        echo "🔓 Non-SSL URLs in configuration.";
    fi
    CACHING="`magentodb -B -e "SELECT value FROM core_config_data WHERE path = 'system/full_page_cache/caching_application'" | grep -v value`"
    if [ "$CACHING" != "fastly" ]; then
        echo "⏳ Caching backend not Fastly ($CACHING)"
    else
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

    REPORTS_SIZE="`(timeout 1s ls -1f var/report || true) | wc -l`"
    if [ "$REPORTS_SIZE" -gt 5 ]; then
        echo "🔕 Reports (low estimate, may slow deploy): $REPORTS_SIZE"
    fi

    CRONSTATUS="`php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); if (empty($config["cron"]["enabled"])) { echo "Disabled"; }'`"
    if [ "$CRONSTATUS" != "" ]; then
        echo "⏲️ Cron is currently $CRONSTATUS" | grep --color=auto Disabled
    fi

    find vendor/ app/ -name default.xml | grep layout | xargs grep --color=auto -E 'cacheable.*false'
    ps axo etime,args | grep -v grep | grep --color=auto -e cron:run -e indexer

    composer show magento/* --working-dir=$HOME 2>/dev/null | grep -e magento/magento-cloud-metapackage -e magento/ece-tools --color=auto
    composer show somethingdigital/magento2-theme-* --working-dir=$HOME 2>/dev/null | grep somethingdigital/magento2-theme-bryantpark --color=auto

    php $HOME/bin/magento indexer:status
    magentodb -e "SELECT status AS cron_status, MAX(executed_at), MAX(scheduled_at), SUBSTRING(GROUP_CONCAT(DISTINCT job_code), 1, 60) AS jobs_truncated FROM cron_schedule GROUP BY status;"

    tail var/log/cloud.log | grep -q var/.deploy_is_failed && tail var/log/cloud.log | grep --color=auto -C99 -e failed -e CRITICAL -e 'returned code 1'
}
