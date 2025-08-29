-- create the databases
CREATE DATABASE IF NOT EXISTS ogspy;

-- create the users for each database
CREATE USER 'admin'@'%' IDENTIFIED BY 'ogsteam';
GRANT CREATE, ALTER, INDEX, LOCK TABLES, REFERENCES, UPDATE, DELETE, DROP, SELECT, INSERT ON `ogspy`.* TO 'admin'@'%';

FLUSH PRIVILEGES;
