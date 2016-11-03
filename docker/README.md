OGSpy Docker

PHP 7.06 Maria DB

Based on work done by tutumcloud
https://github.com/tutumcloud/lamp

Usage :

1. Get the image:

docker pull ogsteam/ogspy

2. Run the image:

docker run -d -p80:80 --name ogspy_container ogsteam/ogspy

3. Install OGSpy with Database settings:

Database : ogspy
user : admin
Password : ogsteam

4. When installation is finished remove install Folder:

docker exec ogspy_container rm -Rf /app/install


