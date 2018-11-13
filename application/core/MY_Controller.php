<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    const HTTP_OK = 200;
    const HTTP_BAD = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_SERVER_ERROR = 500;
    public static $redis = NULL;

    public function __construct()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        parent::__construct();
//        if (is_null(self::$redis)) {
//            self::$redis = self::_connectRedis();
//        }

        $this->lang->load('form_validation');

        if ($this->config->item('global_site_closed')) {
            if (!in_array($this->input->ip_address(), (array)$this->config->item('allowed_ip_for_works'))) {
                $this->load->view('closed');
            }
        }

        if (extension_loaded('newrelic')) {
            newrelic_add_custom_parameter('_GET', json_encode($_GET));
            newrelic_add_custom_parameter('_POST', json_encode($_POST));
        }

        // текущий авторизованный юзер
        //$id = $this->session->userdata('id');
        $id = (empty($_SESSION['user_data']['id'])) ? FALSE : $_SESSION['user_data']['id'];

        if ($id && intval($id) > 0) {
            // @todo need test
            if (extension_loaded('newrelic')) {
                newrelic_add_custom_parameter('user_id', $id);
            }
        }


    }

    public function response($data = array(), $http_code = 200)
    {
        $this->output->set_status_header($http_code);
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
        return TRUE;
    }

    public function __destruct()
    {

    }
}