<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Exceptions Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Exceptions
 * @author        EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class MY_Exceptions extends CI_Exceptions {

    private $CI;


    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct()
    {

        parent::__construct();
    }


    function show_error($heading = 'Error unknown', $message = 'Contact support', $template = 'error', $status_code = 500)
    {
        $templates_path = config_item('error_views_path');

        if (empty($templates_path))
        {
            $templates_path = VIEWPATH . 'errors' . DIRECTORY_SEPARATOR;
        }
        if (is_cli())
        {
            $message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);
            $template = 'cli' . DIRECTORY_SEPARATOR . $template;
        } else
        {
            if($status_code == 400)
            {
                include(FCPATH_GLOBAL . 'errors/custom_404.html');
                return;
            }
            $this->CI =& get_instance(); // not work on 400 error

            if ($status_code == 404)
            {
                //set_status_header($error);
                set_status_header($status_code);
                include(FCPATH_GLOBAL . 'errors/custom_404.html');
                //var_dump($this->CI->load);
            } else
            {
                set_status_header($status_code);
                $data['error_heading'] = $heading;
                $data['error_message'] = $message;
                $data['error_code'] = $status_code;
                //set_status_header($error);
                $s = $this->CI->load->view('error', $data, TRUE);
                //var_dump($this->CI->load);
                echo $s;
                throw new CriticalException('Log SHIT');
            }
            return;
        }
        if (ob_get_level() > $this->ob_level + 1)
        {
            ob_end_flush();
        }
        ob_start();
        include($templates_path . $template . '.php');
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

}

