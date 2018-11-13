<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Model {
    /** @var Sparrow $s  */
    public $s = NULL;
    /** @var CI_Controller $CI  */
    public $CI = NULL;
    protected $id = NULL;
    protected $data = [];
    protected $class_table = NULL;
    public static $redis = NULL;
    public function __construct()
    {
        $this->CI =& get_instance();
//        if(is_null(self::$redis)){
//            self::$redis = self::_connectRedis();
//        }

    }

//    private static function _connectRedis()
//    {
//        $CI =& get_instance();
//        if ($CI->config->load('redis', TRUE, TRUE)) {
//            $config = $CI->config->item('redis');
//            $redis = new Redis();
//            $redis->connect($config['host'], $config['port']);
//            if ($config['password']) {
//                $redis->auth($config['password']);
//            }
//
//            $redis->select($config['db']);
//
//            return $redis;
//        }
//        return FALSE;
//    }

    public function set_id($id = FALSE, $for_update = FALSE)
    {

        if ((int)$id > 0)
        {
            if($for_update){
                $this->data = $this->CI->s->from($this->class_table)->where(['id' => $id])->for_update()->one();
            }else{
                $this->data = $this->CI->s->from($this->class_table)->where(['id' => $id])->one();
            }
            if ( ! empty($this->data))
            {
                $this->_map_sql_to_class();
            } else
            {
                throw new Exception('No data with this id!:'. $id);
            }
        } else
        {
            if($id !== FALSE){
                throw new Exception('Id error!');
            }
        }
        return $this;
    }
    public function get_id()
    {
        return $this->id;
    }
    public function reload($for_update = FALSE)
    {
        return $this->set_id($this->id,$for_update);
    }
    protected function _map_sql_to_class()
    {
        foreach ($this->data as $k => $v)
        {
            $this->{$k} = $v;
        }
    }
    protected function _save($key = NULL, $value = NULL)
    {
        if (is_null($key))
        {
            return FALSE;
        }

        if ($this->is_loaded(TRUE) && $this->get_id() != NULL)
        {
            $this->CI->s->from($this->class_table)->where(['id' => $this->id])->update([$key => $value])->execute();
            return $this->CI->s->affected_rows == 1;
        } else
        {
            return FALSE;
        }
    }

    public function load_data($data){

        foreach ($data as $key => $val){
            if(property_exists($this, $key)){
//                if($_SERVER['REMOTE_ADDR'] == '93.74.231.89'){
//                    var_dump($data);
//                }

                $this->{$key} = $val;
                $this->data[$key] = $val;
            }
        }

        return $this;
    }

    public function is_loaded($hard = FALSE)
    {
        if ($hard)
        {
            if (empty($this->data) || $this->id == NULL)
            {
                throw new Exception('Object not loaded!');
            }
        }
        return ( ! empty($this->data));
    }
    public function __destruct()
    {
    }
}