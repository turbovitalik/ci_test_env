<?php

class CriticalException extends Exception {
} // искючение когда надо вернуть всё назад, что то серьёзное
class WarningException extends Exception {
} // не критичное исключение, от которого хуже не будет

/*
* Name: Universal Analytics Cookie Parser Class
* Description: Parses the new format Universal Analytics cookie.
* Developer: Matt Clarke
* Date: 10.11.2018
*/

class Bootstrap {

    function __construct()
    {

        $this->CI =& get_instance();
        $this->set_db();
        $this->connect_redis();
        //$this->set_tags();

        if ( ! defined('CDN_ADMIN_THEME'))
        {
            define('CDN_ADMIN_THEME', '/adm/assets/');
        }

    }

    function connect_redis()
    {
        if ($this->CI->config->load('redis', TRUE, TRUE)) {
            $config = $this->CI->config->item('redis');
            if(empty($config['host'])){
                return FALSE;
            }

            $redis = new Redis();
            $redis->connect($config['host'], $config['port']);
            if ($config['password']) {
                $redis->auth($config['password']);
            }

            $redis->select($config['db']);

            $this->CI->redis = $redis;
            return  TRUE;
        }
        return FALSE;
    }

    function set_db()
    {
        include_once PACKAGE_PATH . 'vendor/sparrow.php';

        if (file_exists(APPPATH . 'config/' . PROJECT_CONFIG_PATH . 'database.php'))
        {
            require_once(APPPATH . 'config/' . PROJECT_CONFIG_PATH . 'database.php');
        } else
        {
            if (file_exists(APPPATH . 'config/' . PROJECT_CONFIG_PATH . ENVIRONMENT . '/database.php'))
            {
                require_once(APPPATH . 'config/' . PROJECT_CONFIG_PATH . ENVIRONMENT . '/database.php');
            }
        }

        $this->CI->s = new Sparrow();

        $this->CI->s->setDb(array(
            'type' => 'mysqli',
            'hostname' => $db['default']['pconnect'] === TRUE ? ('p:' . $db['default']['hostname']) : ($db['default']['hostname']),
            'database' => $db['default']['database'],
            'username' => $db['default']['username'],// $db['default']['username'],
            'password' => $db['default']['password']
        ));

        $this->CI->s->sql('SET NAMES utf8')->execute();
        return  TRUE;

    }
}
