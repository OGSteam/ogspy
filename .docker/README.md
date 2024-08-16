
# OGSpy Docker

This is the environment to test your development Branch.

It contains all the necessary tools to run OGSpy.:

- Alpine Linux
- NGINX
- Maria DB
- PHP 8.3
- PHPMyAdmin

## Run Locally

Run the Docker Compose to set up the environment.

```bash
  docker.exe compose -f docker-compose.yml -p my-ogspy up -d
```

## Connect to OGSpy

http://127.0.0.1:16005/

User : ogsteam
Password : ogsteam

## Connect to PHPMyAdmin

http://127.0.0.1:16006/

User : root
Password : password

## Volumes

The image will create the following volumes to let you see your files in your local environment.
- `ogspy-db` - MariaDB
- `ogspy-app` - NGINX + OGSPY

## Customize your image

You can edit the `Dockerfile.ogspy` file to customize your image and select a customer branch to test and the PHP Version.
