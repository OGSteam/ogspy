# OGSpy Docker

PHP 7.06 Maria DB

Based on work done by tutumcloud
https://github.com/tutumcloud/lamp

## Usage

-   Get the image: `docker pull ogsteam/ogspy`

-   Run the image: `docker run -d -p80:80 --name ogspy_container ogsteam/ogspy`

-   Install OGSpy with Database settings:  
  Open with your browser http://127.0.0.1 (The page could appear after some time corresponding to the service startup time)

    >   Database : ogspy  
    > user : ogsteam  
    > Password : password

-   When installation is finished remove install Folder:
  `docker exec ogspy_container rm -Rf /app/install`
