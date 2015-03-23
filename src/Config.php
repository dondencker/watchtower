<?php  namespace Dencker\Watchtower; 


class Config {
    public static function getPrimaryActor()
    {
        return self::get('primary_actor');
    }

    /**
     * @param $config_path
     *
     * @return mixed
     */
    private static function get($config_path)
    {
        return config( "watchtower.{$config_path}" );
    }

}