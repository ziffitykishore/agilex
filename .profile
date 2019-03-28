# Setup some basic things for easier usage in SSH when necessary.

export PATH="$HOME/bin:$PATH"

function magentodb {
    `php -r '$config = require(getenv("HOME") . "/app/etc/env.php"); $db = $config["db"]["connection"]["default"]; echo "mysql -h$db[host] -u$db[username] $db[dbname]"; if ($db["password"]) { echo " -p" . $db["password"]; }'` "$@"
}

function magentostat {
    if [ `magento maintenance:status | grep -q 'not active'` ]; then
        echo "Maintenance mode active."
    fi
    if [ `magento app:config:status | grep -q 'are up to date'` ]; then
        echo "Config files not up to date."
    fi
    if [ `magento setup:db:status | grep -q 'All modules are up to date'` ]; then
        echo "Database needs update."
    fi
    if [ "$MAGENTO_CLOUD_BRANCH" = "production" -o "$MAGENTO_CLOUD_BRANCH" = "staging" ]; then
        DBSTATUS="`magentodb -B -e "SHOW GLOBAL STATUS LIKE 'wsrep_local_state_comment'" | grep wsrep | gawk '{ print $2; }'`"
        DBNODES="`magentodb -B -e "SHOW GLOBAL STATUS LIKE 'wsrep_cluster_size'" | grep wsrep | gawk '{ print $2; }'`"
        if [ "$DBSTATUS" != "Synced" ]; then
            echo "Database sync status: $DBSTATUS"
        fi
        if [ "$DBNODES" != "3" ]; then
            echo "Database nodes missing?  Current: $DBNODES"
        fi
        df --output -h /data/* /
    else
        df --output -h /mnt /
    fi
    magento indexer:status
    magentodb -e "SELECT status AS cron_status, MAX(executed_at), MAX(scheduled_at), SUBSTRING(GROUP_CONCAT(DISTINCT job_code), 1, 60) AS jobs_truncated FROM cron_schedule GROUP BY status;"
}
