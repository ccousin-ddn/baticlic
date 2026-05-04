<?php

class autoloader {

    public static $loader;

    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    public function __construct()
    {
        spl_autoload_register(array($this,'models'));
        spl_autoload_register(array($this,'controllers'));
        spl_autoload_register(array($this,'libraries'));
    }

    public function models($class)
    {       
        set_include_path(PHP_ROOT.'models');
        spl_autoload_extensions('.php');
        spl_autoload("m_" . $class);
    }

    public function controllers($class)
    {
		set_include_path(PHP_ROOT.'controllers');
        spl_autoload_extensions('.php');
        spl_autoload("c_" . $class);
    }

    public function libraries($class)
    {
        set_include_path(PHP_ROOT.'libraries');
        spl_autoload_extensions('.php');
        spl_autoload("lib_bs_" . $class);
		spl_autoload("lib_" . $class);
    }
}

//call
autoloader::init();