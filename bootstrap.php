<?php
/**
 * Autoloader PSR-4
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \CaptainJas\Foo\Bar\Baz\Qux class
 * from /Foo/Bar/Baz/Qux.php:
 *
 *      new \CaptainJas\Foo\Bar\Baz\Qux;
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'CaptainJas\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

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
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

define('JAS_ROOT', dirname(__FILE__));

if(!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}