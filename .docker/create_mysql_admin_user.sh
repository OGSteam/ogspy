#!/bin/bash

/usr/bin/mysqld_safe > /dev/null 2>&1 &

RET=1
while [[ RET -ne 0 ]]; do
    echo "=> Waiting for confirmation of MySQL service startup"
    sleep 5
    mysql -uroot -e "status" > /dev/null 2>&1
    RET=$?
done

PASS=${MYSQL_PASS:-$(pwgen -s 12 1)}
_word=$( [[ ${MYSQL_PASS} ]] && echo "preset" || echo "random" )
echo "=> Creating MySQL admin user with ${_word} password"

mysql -uroot -e "CREATE USER 'admin'@'localhost' IDENTIFIED BY 'ogsteam'"
mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION"
#mysql -uroot -e "GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' IDENTIFIED VIA unix_socket WITH GRANT OPTION"

mysql -uroot -e "CREATE DATABASE ogspy"
# You can create a /mysql-setup.sh file to intialized the DB
if [[ -f /app/.docker/mysql-setup.sh ]]; then
  . /app/.docker/mysql-setup.sh
fi

echo "=> Done!"

echo "========================================================================"
echo "You can now connect to this MySQL Server using:"
echo ""
echo "    mysql -uadmin -p$PASS -h<host> -P<port>"
echo ""
echo "Please remember to change the above password as soon as possible!"
echo "MySQL user 'root' has no password but only allows local connections"
echo "========================================================================"

mysqladmin -uroot shutdown
