<?php
namespace CaptainJas;

class CaptainJas
{
    protected $_config = null;

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

    protected function _defineConsts()
    {
        $this->_defineConst('JAS_ROOT', dirname(__FILE__));
        $this->_defineConst('DS', DIRECTORY_SEPARATOR);
    }

    protected function _defineConst($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    static public function p($key)
    {
        if (PHP_SAPI === 'cli') {
            $_GET = getopt('', array($key . '::'));
        }
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public function getConfig()
    {
        if (!$this->_config) {
            $json_data = file_get_contents('config.json');
            if ($json_data === false) {
                throw new \RuntimeException('You must deploy a config.json file. Try to copy config.json.dist as a basis');
            }
            $this->_config = json_decode($json_data);
        }
        return $this->_config;
    }

    public function getHook($ns, $class = '', $args = array())
    {
        return $this->_getClass($ns, 'hook', $class, $args);
    }

    /**
     * @param string $ns namespace code (jas only for now)
     * @param string $type type of the connector (hook, watcher, sender, ...)
     * @param string $class class to load (basecamp_events_message, subversion_commit_message,...)
     * @param array $args params to pass to connector constructor
     * @throws \BadMethodCallException
     */
    protected function _getClass($ns, $type, $class = '', $args = array())
    {
        if (!$class) {
            list($ns, $class) = explode('|', $ns);
        }

        if ($ns != 'jas') {   //@TODO implement extension mechanism
            throw new \BadMethodCallException('@TODO implement extension mechanism');
        } else {
            $namespace = '\\CaptainJas\\Connectors\\';
        }

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
     * @param $type
     * @return mixed
     */
    protected function ucWords($type)
    {
        return str_replace(' ', '\\', ucwords(str_replace('_', ' ', $type)));
    }

    public function getWatcher($ns, $class, $args = array())
    {
        return $this->_getClass($ns, 'watcher', $class, $args);
    }

    public function getSender($ns, $class, $args = array())
    {
        return $this->_getClass($ns, 'sender', $class, $args);
    }
}