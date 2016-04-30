<?php
/**
 * Sf is a helper class serving common framework functionalities.
 * @author Harry Sun <sunguangjun@126.com>
 */
class Sf
{
    /**
     * Creates a new object using the given configuration.
     * You may view this method as an enhanced version of the `new` operator.
     * @param string $name the object name
     */
    public static function createObject($name)
    {
        $config = require(SF_PATH . "/config/$name.php");
        // create instance
        $instance = new $config['class']();
        unset($config['class']);
        // add attributes
        foreach ($config as $key => $value) {
            $instance->$key = $value;
        }
        $instance->init();
        return $instance;
    }
}
