<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Tests extends MY_Controller
{
    protected $response_data;

    public function __construct()
    {
        parent::__construct();

        $this->CI =& get_instance();
        $this->load->model('news_model');

        $this->response_data = new stdClass();
        $this->response_data->status = 'success';
        $this->response_data->error_message = '';
        $this->response_data->data = new stdClass();

        if (ENVIRONMENT === 'production')
        {
            die('Access denied!');
        }
    }
    // костыль
    public function index()
    {
        $this->get_last_news();
    }
    public function get_last_news()
    {
        $this->response_data->data->news = News_model::get_all('short_info');
        $this->response_data->data->patch_notes = '';
        $this->response($this->response_data);
    }

    
}
