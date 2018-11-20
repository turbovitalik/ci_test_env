<?php

class News_Comments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('news_comment_model');
    }

    public function store()
    {
        $postData = json_decode($this->input->raw_input_stream, true);

        $this->news_comment_model->insert_comment($postData);

        $this->response('Comment has been added');
    }
}