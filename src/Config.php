<?php  namespace Dencker\Watchtower; 


class Config {

    protected static $instance;

    public static function getInstance()
    {
        return self::$instance ?: self::$instance = new self;
    }

    public static function getPrimaryActor()
    {
        return self::getInstance()->get('primary_actor');
    }

    /**
     * @param $config_path
     *
     * @return mixed
     */
    public function get($config_path)
    {
        return config( "watchtower.{$config_path}" );
    }

    /**
     * @param mixed $instance
     */
    public static function setInstance(self $instance)
    {
        self::$instance = $instance;
    }

}