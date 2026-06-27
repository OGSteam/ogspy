
# OGSpy Docker Stack

This stack is prepared for container deployment (including Portainer) with the OGSpy installer flow.

It includes:
- NGINX
- PHP-FPM (OGSpy)
- MariaDB
- Optional PHPMyAdmin profile for troubleshooting

## Production-first behavior

- Database is no longer pre-seeded from `.docker/db/`; installer creates schema and admin account.
- Credentials are provided through environment variables.
- Installer runs automatically on first boot and is locked after install by default.

## Required environment variables

Set these in Portainer stack env (or a local `.env` file):

```env
MARIADB_ROOT_PASSWORD=change-this-root-password
MARIADB_PASSWORD=change-this-app-password
MARIADB_DATABASE=ogspy
MARIADB_USER=ogspy
OGSPY_HTTP_PORT=16005
```

## Automatic first-run install

By default, first boot runs the CLI installer automatically.

Default admin credentials:

```env
OGSPY_ADMIN_USER=ogsteam
OGSPY_ADMIN_PASSWORD=ogsteam
```

You can override them:

```env
OGSPY_AUTO_INSTALL_ON_START=true
OGSPY_ADMIN_USER=admin
OGSPY_ADMIN_PASSWORD=change-this-admin-password
OGSPY_ADMIN_EMAIL=admin@example.com
```

Optional overrides:

```env
OGSPY_DB_HOST=db
OGSPY_TABLE_PREFIX=ogspy_
```

Behavior:

- If `install/install.lock` exists, auto-install is skipped.
- If table `<prefix>config` exists, auto-install is skipped.
- If required admin variables are missing, startup uses default `ogsteam/ogsteam` unless overridden.

## Deploy

```bash
docker compose -f .docker/docker-compose.yml up -d
```

## Portainer copy/paste stack (recommended)

For Portainer web stacks, use prebuilt images instead of `build:` to avoid context/build issues.

Stack file:

- `.docker/docker-compose.portainer.yml`

Required extra variables for this stack:

```env
OGSPY_APP_IMAGE=ghcr.io/ogsteam/ogspy-app:latest
OGSPY_NGINX_IMAGE=ghcr.io/ogsteam/ogspy-nginx:latest
```

Build and push once (from repository root), then reuse in Portainer:

```bash
docker build -f .docker/Dockerfile.ogspy -t ghcr.io/ogsteam/ogspy-app:latest .
docker build -f .docker/Dockerfile.nginx -t ghcr.io/ogsteam/ogspy-nginx:latest .docker
docker push ghcr.io/ogsteam/ogspy-app:latest
docker push ghcr.io/ogsteam/ogspy-nginx:latest
```

Then in Portainer:

1. Create Stack.
2. Paste content of `.docker/docker-compose.portainer.yml`.
3. Set all environment variables in the Portainer UI.
4. Deploy the stack.

Open OGSpy login page at:

http://127.0.0.1:16005/

## Lock installer after first setup

After installation, lock it with one of these methods:

1. Use OGSpy install UI lock button (recommended).
2. Set `OGSPY_LOCK_INSTALL_ON_START=true` and redeploy the stack.
3. Create the lock file from inside the app container:

```bash
touch install/install.lock
```

## Optional PHPMyAdmin

The `phpmyadmin` service uses profile `debug` and is disabled by default.

Enable only when needed:

```bash
docker compose -f .docker/docker-compose.yml --profile debug up -d phpmyadmin
```

## Volumes

- `ogspy-db`: MariaDB data
- `ogspy-app`: OGSpy application files shared between PHP and NGINX
