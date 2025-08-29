# PhpStorm Configuration for OGSpy DevContainer

## Setup Instructions

### 1. Open Project in PhpStorm
- Open PhpStorm
- Go to File > Open and select the ogspy folder
- PhpStorm will detect the devcontainer configuration

### 2. Docker Configuration
- PhpStorm will automatically configure Docker integration
- The PHP interpreter will be set to use the container's PHP 8.4

### 3. Database Connection
Pre-configured connection available:
- **Host**: db (or localhost:3306 from host)
- **Database**: ogspy
- **Username**: ogspy_user
- **Password**: ogspy_password

### 4. Xdebug Setup
Xdebug is pre-configured with:
- **Port**: 9003
- **IDE Key**: PHPSTORM
- **Auto-start**: Enabled

### 5. Code Quality Tools
The following tools are available:
- **PHP_CodeSniffer**: PSR-12 standard
- **PHPStan**: Static analysis
- **Composer**: Dependency management

### 6. Useful Shortcuts
- **Ctrl+Shift+F10**: Run current file
- **Ctrl+Shift+F9**: Debug current file
- **Alt+F8**: Evaluate expression during debugging
- **Ctrl+Alt+L**: Reformat code

### 7. Nginx Configuration
The container uses Nginx + PHP-FPM:
- Web server accessible at: http://localhost:8080
- Configuration file: `.devcontainer/nginx.conf`

### 8. Recommended Plugins (Auto-installed)
- PHP Tools
- Database Tools
- Docker Integration
- Nginx Support
- YAML Support

## Troubleshooting

### If PHP interpreter is not detected:
1. Go to File > Settings > PHP
2. Add new interpreter: Docker Compose
3. Select the `app` service from docker-compose.yml

### If Xdebug doesn't work:
1. Check Run > Edit Configurations
2. Ensure "Listen for PHP Debug Connections" is enabled
3. Verify port 9003 is not blocked

### For database connection issues:
1. Ensure containers are running: `docker-compose ps`
2. Check connection from container: `docker-compose exec app mysql -h db -u ogspy_user -p`
