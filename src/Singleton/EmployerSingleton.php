<?php
namespace App\Singleton;

use Exception;

class EmployerSingleton
{
    private static ?EmployerSingleton $employer = null;

    /**
     * gets the employer via lazy initialization (created on first usage)
     */
    public static function getEmployer(): EmployerSingleton
    {
        if (static::$employer === null) {
            static::$employer = new static();
        }

        return static::$employer;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple employers,
     * to use the singleton, you have to obtain the employer from Singleton::getEmployer() instead
     */
    private function __construct()
    {
    }

    /**
     * prevent the employer from being cloned (which would create a second employer of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second employer of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
