<?php
namespace CaptainJas;

use CaptainJas\Connectors\Hook\HookAbstract;
use CaptainJas\Connectors\Sender\SenderAbstract;
use CaptainJas\Connectors\Watcher\WatcherAbstract;

/**
 * Main Class
 * @package CaptainJas
 */
class CaptainJas
{
    protected $_config = null;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->_registerAutoload();
        $this->_defineConsts();
    }

    /**
     * Autoloader PSR-4
     *
     * After registering this autoload function with SPL, the following line
     * would cause the function to attempt to load the \CaptainJas\Foo\Bar\Baz\Qux class
     * from /Foo/Bar/Baz/Qux.php:
     *
     *      new \CaptainJas\Foo\Bar\Baz\Qux;
     */
    protected function _registerAutoload()
    {
        /**
         * @param string $class The fully-qualified class name.
         * @return void
         */
        spl_autoload_register(
            function ($class) {

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
            }
        );
    }

    /**
     * Define constants
     */
    protected function _defineConsts()
    {
        $this->_defineConst('JAS_ROOT', dirname(__FILE__));
        $this->_defineConst('DS', DIRECTORY_SEPARATOR);
    }

    /**
     * Define a constant if not already defined
     *
     * @param string $name Name of the const to define
     * @param string $value Value of the const
     */
    protected function _defineConst($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Static function to retreive a parameter from $_GET if HTTP request or from CLI parameters
     *
     * @param   string $key Name of the parameter
     * @return  null|string
     */
    static public function p($key)
    {
        if (PHP_SAPI === 'cli') {
            $_GET = getopt('', array($key . '::'));
        }
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    /**
     * @param string $ns namespace code
     * @param string $class class to load (basecamp_events_message, subversion_commit_message,...)
     * @param array $args params to pass to connector constructor
     * @return HookAbstract of wanted class
     *
     * @throws \BadMethodCallException
     */
    public function getHook($ns, $class = '', $args = array())
    {
        return $this->_getClass($ns, 'hook', $class, $args);
    }

    /**
     * @param string $ns namespace code
     * @param string $type type of the connector (hook, watcher, sender, ...)
     * @param string $class class to load (basecamp_events_message, subversion_commit_message,...)
     * @param array $args params to pass to connector constructor
     * @return mixed of wanted class
     *
     * @throws \BadMethodCallException
     */
    protected function _getClass($ns, $type, $class = '', $args = array())
    {
        if (!$class) {
            list($ns, $class) = explode('|', $ns);
        }

        if (!isset($this->getConfig()->connectors->$ns)) {
            throw new \BadMethodCallException($ns . ' is not a valid namespace defined in config.json');
        }
        $namespace = $this->getConfig()->connectors->$ns;

        $typePath = $this->ucWords($type);
        $className = $this->ucWords($class);

        $fullClass = $namespace . $typePath . '\\' . $className;

        if (!class_exists($fullClass)) {
            throw new \BadMethodCallException('Class ' . $fullClass . ' not found.');
        }

        $rc = new \ReflectionClass($fullClass);
        return $rc->newInstanceArgs($args);
    }

    /**
     * @return \stdClass
     *
     * @throws \RuntimeException
     */
    public function getConfig()
    {
        if (!$this->_config) {
            $json_data = file_get_contents(JAS_ROOT . DS . 'config.json');
            if ($json_data === false) {
                throw new \RuntimeException(
                    'You must deploy a config.json file. Try to copy config.json.dist as a basis'
                );
            }
            $this->_config = json_decode($json_data);
        }
        return $this->_config;
    }

    /**
     * @param $type
     * @return mixed
     */
    protected function ucWords($type)
    {
        return str_replace(' ', '\\', ucwords(str_replace('_', ' ', $type)));
    }

    /**
     * @param string $ns namespace code
     * @param string $class class to load (basecamp_events_message, subversion_commit_message,...)
     * @param array $args params to pass to connector constructor
     * @return WatcherAbstract of wanted class
     *
     * @throws \BadMethodCallException
     */
    public function getWatcher($ns, $class, $args = array())
    {
        return $this->_getClass($ns, 'watcher', $class, $args);
    }

    /**
     * @param string $ns namespace code
     * @param string $class class to load (basecamp_events_message, subversion_commit_message,...)
     * @param array $args params to pass to connector constructor
     * @return SenderAbstract of wanted class
     *
     * @throws \BadMethodCallException
     */
    public function getSender($ns, $class, $args = array())
    {
        return $this->_getClass($ns, 'sender', $class, $args);
    }
}