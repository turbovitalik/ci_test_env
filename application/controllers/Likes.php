<?php

class Likes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('like_model');
    }

    public function save()
    {
        //TODO: fix this
        $postData = json_decode($this->input->raw_input_stream, true);

        $likeType = $postData['type'];
        $itemId = (int) $postData['id'];
        $userId = (int) $postData['user_id'];

        $this->like_model->create_like($likeType, $itemId, $userId);

        $this->response(['result' => 'ok']);
    }

    public function remove()
    {
        $postData = json_decode($this->input->raw_input_stream, true);

        $likeType = $postData['type'];
        $itemId = (int) $postData['id'];
        $userId = (int) $postData['user_id'];

        $this->like_model->remove_like($likeType, $itemId, $userId);

        $this->response(['result' => 'ok']);
    }
}