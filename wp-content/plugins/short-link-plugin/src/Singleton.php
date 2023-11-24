namespace Gorjm\ShortLinkPlugin;

class Singleton {
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
}
