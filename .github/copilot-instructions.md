# OGSpy – AI Coding Agent Instructions

## Project Overview
OGSpy is a PHP-based web application for managing and analyzing data from the OGame universe. It features modular architecture, user/group management, data simulation, and supports extensive plugin/extension development.

## Architecture & Key Components
- **Core Logic**: Main logic resides in `core/`, with helpers and abstract classes for extensibility.
- **Models**: Data access and business logic are in `model/` (e.g., `Player_Model.php`, `Config_Model.php`).
- **Views**: UI templates are in `views/`, using PHP for dynamic rendering (see `home_simulation.php`).
- **Includes**: Shared functions and utilities in `includes/` (e.g., `functions.php`, `config.php`).
- **Mods**: Extensions live in `mod/`, each in its own subfolder, following a convention for modularity.
- **Install & Migration**: The `install/` folder contains auto-upgrade, migration, and config generation logic. Migrations use timestamped filenames (`YYYYMMDDNNN_description.php`).
- **Languages**: Multi-language support via `lang/` and subfolders for each locale.
- **Assets**: Images in `images/`, JS in `js/`, CSS in `skin/`.

## Developer Workflows
- **Local Development**:
  - Use Docker (`.docker/`, `.docker-dev/`) or DevContainer (`.devcontainer/`) for consistent PHP/MariaDB environments.
  - Start with `docker.exe compose -f docker-compose.yml -p my-ogspy up -d` (see `.docker/README.md`).
  - Access app at `http://127.0.0.1:16005/` (default user/pass: ogsteam/ogsteam).
- **Installation & Upgrades**:
  - Use web installer (`install/index.php`) or CLI (`install/upgrade_cli.php`).
  - Database migrations are managed automatically; new migrations go in `install/migrations/`.
- **Configuration**:
  - Main config files: `config/id.php`, `config/key.php`, `config/salt`.
  - Environment variables for containers: see `.devcontainer/README.md`.
- **Testing**:
  - PHPUnit config in `phpunit.xml`. Tests live in `tests/`.
  - Run tests via `vendor/bin/phpunit`.
- **Debugging**:
  - Xdebug enabled in dev containers. Logs in `logs/`.

## Project-Specific Patterns & Conventions
- **Modular Extensions**: Mods follow a folder-per-extension pattern in `mod/`. Each mod can have its own controllers, views, and config.
- **Data Flow**: Models fetch data, views render tables/forms, includes provide shared logic. Example: `home_simulation.php` uses `Player_Model` and helper functions to build dynamic tables.
- **Language Strings**: Use `$lang[...]` for UI text, loaded from `lang/`.
- **Booster/Extension Handling**: See `booster_decode()` usage in simulation views for handling planet extensions.
- **Versioning**: Database migrations use timestamped filenames for ordering and traceability.

## Integration Points
- **External**: OGame data import, Monolog for logging, MariaDB for storage.
- **Internal**: Mods communicate via shared models and helpers. Use provided APIs for user/session management.

## Global Variables: `$log` and `$db`

- **$log**: Global Monolog logger instance, initialized in `common.php` as `new Logger('OGSpy')`. Used for application-wide logging (info, warning, error, debug) and error handling. Accessible in all scripts via `global $log;`. Additional loggers (`$logSQL`, `$logSlowSQL`) exist for SQL and slow query logging.
  - Example usage: `$log->info("Message", [...context...]);`
  - Error handling: Exception traces are logged via Whoops integration in `common.php`.
  - Log files are stored in `logs/`.

- **$db**: Global database connection, singleton instance of `sql_db` (see `includes/mysql.php`). Initialized in `common.php` after config is loaded. Accessed via `global $db;` in scripts and models.
  - Example usage: `$db->query($sql);`
  - Connection parameters are loaded from `config/id.php`.
  - Handles all MySQL interactions for the app.

Always declare `global $log, $db;` at the top of functions or methods that use these variables.

## Folder Conventions: `includes/` vs `core/`

- The `includes/` folder contains legacy feature and utility code. Many older functions, helpers, and shared logic are found here.
- The `core/` folder is the modern location for new features, helpers, and abstractions. New development should prefer `core/` for extensibility and maintainability.
- When refactoring or adding new capabilities, use `core/` unless maintaining or patching legacy code.

## Examples
- To add a new mod: create a folder in `mod/`, add entry points, config, and views following existing mod structure.
- To add a migration: create `install/migrations/YYYYMMDDNNN_description.php` with a migration class.
- To add a language: create a new folder in `lang/` and provide translations for required keys.

## References
- Main entry: `index.php`
- Core logic: `core/`, `model/`, `includes/`
- Install/migration: `install/`
- Mods: `mod/`
- Language: `lang/`
- Assets: `images/`, `js/`, `skin/`

---
For questions or missing conventions, see [Wiki OGSteam](https://wiki.ogsteam.eu) or ask for clarification in the project forum.
