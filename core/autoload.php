<?php
/**
 * Class Autoloader
 * @package OGSpy
 * @subpackage Autoloader
 * @author DarkNoon based on PSR Work
 * @copyright Copyright &copy; 2016, http://ogsteam.fr/
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 3.4.0
 */
namespace Ogsteam\Ogspy;

if (!defined('IN_SPYOGAME')) {
    die("Hacking attempt");
}

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Ogsteam\\Ogspy\\';

    // base directory for the namespace prefix
    $model_base_dir = __DIR__ . '/../model/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $model_base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});