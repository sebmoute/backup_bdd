# backup_bdd
php script for backuping mysql database to a sFTP server

# install :

1) complete credentials and paths for both database and sFTP Server in backup_bdd.php
2) upload it on your server and CRON it
3) (Optional) copy shell script on the chosen path in your sFTP server and CRON it to remove all backups in the path except last 30