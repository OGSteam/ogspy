# OGSpy – AI Coding Agent Instructions

## Development Philosophy: Keep It Simple (KISS) + Baby Steps

### Core Principles
- **Keep It Simple**: Always choose the simplest solution that works. Avoid over-engineering or premature optimization.
- **Baby Steps**: Break complex tasks into small, manageable, testable increments.
- **Todo-Driven Development**: Use structured todo lists to organize work, track progress, and ensure nothing is forgotten.
- **One Thing at a Time**: Focus on completing one todo item before moving to the next.
- **Test Early, Test Often**: Validate each baby step to catch issues immediately.

### Working Methodology
1. **Break Down Complex Requests**: When users ask for complex features, decompose them into logical, sequential baby steps
2. **Create Todo Lists**: Use the manage_todo_list tool for any non-trivial work (anything requiring >1 action)
3. **Work Incrementally**: Complete one todo, test it, mark it done, then move to the next
4. **Validate Each Step**: After each todo completion, verify the change works before proceeding
5. **Keep User Informed**: Todo lists provide transparency about progress and next steps

### When to Use Todo Lists
- ✅ **Multi-step features** (authentication system, new modules, refactoring)
- ✅ **Bug fixes requiring investigation** (debug → identify → fix → test)
- ✅ **Database schema changes** (design → migrate → update models → test)
- ✅ **UI/UX improvements** (mockup → implement → style → test)
- ❌ **Single file edits** (fix typo, add one function)
- ❌ **Simple questions** (explain code, show usage)

### Example Todo Breakdown
Instead of "Add user authentication":
1. Design authentication flow and database schema
2. Create migration for user authentication tables
3. Implement User_Auth_Model with login/logout methods
4. Create login form view and controller
5. Add session management and middleware
6. Update navigation to show login/logout options
7. Add user registration functionality
8. Test complete authentication flow

## Project Overview
OGSpy is a PHP-based web application for managing and analyzing data from the OGame universe. It features modular architecture, user/group management, data simulation, and supports extensive plugin/extension development.

## Architecture & Key Components
- **Core Logic**: Main logic resides in `core/`, with helpers and abstract classes for extensibility.
- **Models**: Data access and business logic are in `model/` (e.g., `Player_Model.php`, `Config_Model.php`). Uses namespaced OOP pattern with `Ogsteam\Ogspy\Model` namespace extending `Model_Abstract`.
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
  - PHPUnit config in `phpunit.xml`. Tests live in `tests/unit/`.
  - Run tests via `vendor/bin/phpunit`.
  - Coverage includes `includes/` and `core/` directories.
- **Debugging**:
  - Xdebug enabled in dev containers. Logs in `logs/`.
- **CI/CD**:
  - GitHub Actions workflows in `.github/workflows/` (release.yml, sonarcloud.yml).
  - Automated releases on develop/master branches with semantic versioning.
  - Conventional commits supported via commitizen (`npm run commit`).

## Project-Specific Patterns & Conventions
- **Modular Extensions**: Mods follow a folder-per-extension pattern in `mod/`. Each mod can have its own controllers, views, and config.
- **Data Flow**: Models fetch data, views render tables/forms, includes provide shared logic. Example: `home_simulation.php` uses `Player_Model` and helper functions to build dynamic tables.
- **Language Strings**: Use `$lang[...]` for UI text, loaded from `lang/`.
- **Booster/Extension Handling**: See `booster_decode()` usage in simulation views for handling planet extensions.
- **Versioning**: Database migrations use timestamped filenames for ordering and traceability.

## Version Management & Migrations

OGSpy uses an automated migration system with intelligent version synchronization:

### Version Files
- **`install/version.php`**: Contains `$ogspy_version` (application version) and `$database_version` (migration schema version)
- **Application version**: User-facing version (e.g., "4.0.2") 
- **Database version**: Migration timestamp for schema/data changes (e.g., "20251201001")

### Automated Version Synchronization
- **Smart Detection**: The `MigrationManager` automatically compares `$ogspy_version` in `version.php` with the version stored in the `ogspy_config` table
- **Auto-Sync**: If versions differ, the system automatically updates the database version to match the file version
- **No Manual Migrations**: Simple version bumps require NO migration files - just update `$ogspy_version`

### Migration Creation Rules
- **Schema/Data Changes**: Create migrations in `install/migrations/` with timestamp format `YYYYMMDDNNN_description.php`
- **Version-Only Changes**: Simply update `$ogspy_version` in `version.php` - no migration needed
- **Migration Structure**: Classes follow pattern `Migration_YYYYMMDDNNN_Description` with `getVersion()`, `getDescription()`, `up()`, and `down()` methods

### Version Update Process
1. **Simple Version Bump**: Edit `$ogspy_version = "4.0.3"` in `install/version.php`
2. **With Schema Changes**: Also increment `$database_version` and create corresponding migration files
3. **Automatic Detection**: System detects changes during upgrade/installation and syncs appropriately

### CLI Tools
- **Status Check**: `php install/upgrade_cli.php check` - Shows pending migrations and version status
- **Auto Upgrade**: `php install/upgrade_cli.php upgrade` - Executes migrations and version sync
- **Installation**: Full automated installation with `php install/upgrade_cli.php install [params]`

### Requirements
- **PHP**: Minimum version 8.1 (configured in `composer.json`, CLI, and web installer)
- **Extensions**: Required extensions verified: `mysqli`, `json`, `mbstring`, `openssl`, `zlib`, `zip`

## Integration Points
- **External**: OGame data import, Monolog for logging, MariaDB for storage.
- **Internal**: Mods communicate via shared models and helpers. Use provided APIs for user/session management.
- **MCP Server**: MariaDB MCP server integration for database operations (see `package.json` dependencies).
- **Composer Dependencies**: Tooltipster for UI tooltips, Whoops for error handling, Monolog for structured logging.

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

## Code Patterns & Examples

### Model Pattern Example
```php
namespace Ogsteam\Ogspy\Model;
use Ogsteam\Ogspy\Abstracts\Model_Abstract;

class Your_Model extends Model_Abstract {
    public function get_data(int $id) {
        $request = "SELECT * FROM " . TABLE_PREFIX . "your_table WHERE id = " . $id;
        $result = $this->db->sql_query($request);
        return $this->db->sql_fetch_assoc($result);
    }
}
```

### Global Variables Usage
```php
function your_function() {
    global $log, $db;
    $log->info("Operation started");
    $result = $db->sql_query($sql);
    // ... process result
}
```

## Examples
- **New Mod**: Create a folder in `mod/`, add entry points, config, and views following existing mod structure.
- **Version Bump**: Edit `$ogspy_version = "4.1.0"` in `install/version.php` - automatic sync handles the rest.
- **Schema Migration**: Increment `$database_version = "20251201001"` and create `install/migrations/20251201001_add_new_feature.php` with migration class.
- **New Language**: Create a new folder in `lang/` and provide translations for required keys.
- **Extension Requirements**: Add to `composer.json` require section and update CLI/web installer verification arrays.

## OGSpy-Specific Development Patterns

### Todo-Driven Feature Development
When implementing new features in OGSpy, follow these patterns:

#### Model-View-Controller Pattern
1. **Design Data Layer**: Plan database schema changes and model updates
2. **Create Migration**: Add timestamped migration file if schema changes needed  
3. **Update Models**: Modify relevant model classes (e.g., `Player_Model.php`, `Config_Model.php`)
4. **Build Views**: Create or update PHP templates in `views/`
5. **Add Controllers**: Implement business logic and request handling
6. **Test Integration**: Verify complete feature works with existing system

#### Mod Development Pattern  
1. **Plan Mod Structure**: Design folder structure in `mod/yourmod/`
2. **Create Entry Points**: Add main controller and configuration files
3. **Build Data Models**: Create mod-specific database tables and models
4. **Develop Views**: Create user interface templates  
5. **Add Language Support**: Create translation files in appropriate `lang/` folders
6. **Test Mod Integration**: Verify mod works with core OGSpy system

#### Database Migration Pattern
1. **Analyze Current Schema**: Review existing tables and relationships
2. **Design Changes**: Plan new tables, columns, or data transformations
3. **Create Migration File**: Use timestamp format `YYYYMMDDNNN_description.php`
4. **Implement Up/Down Methods**: Code both upgrade and rollback logic
5. **Test Migration**: Verify migration works forward and backward
6. **Update Models**: Modify related model classes to use new schema

#### Version Management Pattern
1. **Check Current Version**: Review `install/version.php` for current state
2. **Plan Version Bump**: Decide if simple version bump or migration needed
3. **Update Version File**: Modify `$ogspy_version` in `version.php`
4. **Create Migrations**: Add migration files only if schema/data changes needed
5. **Test Upgrade Path**: Verify automated version sync works correctly
6. **Validate Installation**: Test both fresh install and upgrade scenarios

### Testing and Validation Best Practices
- **After Each Todo**: Run relevant tests or manual validation
- **Database Changes**: Use CLI tools to verify migrations work
- **Mod Updates**: Test in clean OGSpy installation  
- **UI Changes**: Check in multiple browsers and screen sizes
- **Performance**: Monitor logs for slow queries or errors

## References
- Main entry: `index.php`
- Core logic: `core/`, `model/`, `includes/`
- Install/migration: `install/`
- Mods: `mod/`
- Language: `lang/`
- Assets: `images/`, `js/`, `skin/`

---
For questions or missing conventions, see [Wiki OGSteam](https://wiki.ogsteam.eu) or ask for clarification in the project forum.
