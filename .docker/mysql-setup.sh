#!/bin/sh
echo "=> Initializing Data for OGSpy DB"
mysql -uroot < /ogspy_docker.sql
echo "=> Populate OGSpy DB Done!"
