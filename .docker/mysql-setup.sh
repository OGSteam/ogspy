#!/bin/bash
echo "=> Initializing Data for OGSpy DB"
mysql -uroot < /app/ogspy_docker.sql
echo "=> Populate OGSpy DB Done!"