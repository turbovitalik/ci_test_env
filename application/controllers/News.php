<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class News extends MY_Controller
{
    /**
     * @var News_model
     */
    public $news_model;


    public function __construct()
    {

        parent::__construct();

        $this->CI =& get_instance();
        $this->load->model('news_model');
        $this->load->model('news_comment_model');
        $this->load->model('like_model');
        $this->load->library('comments_helper');

        $this->response_data = new stdClass();
        $this->response_data->status = 'success';
        $this->response_data->error_message = '';
        $this->response_data->data = new stdClass();

        if (ENVIRONMENT === 'production') {
            die('Access denied!');
        }

    }

    public function news()
    {
        $news = $this->news_model->get_last_news(3);

        $this->response($news);
    }

    public function news_single($id)
    {
        $news = $this->news_model->get_by_id($id);

        $news['isLiked'] = $this->like_model->is_liked_by_user($this->user, $id, 'news') ? 1 : 0;

        $comments = $this->news_comment_model->get_news_comments($id);

        $commentsLikedByUser = $this->like_model->get_liked_by_user($this->user, $id, 'comment');

        $commentsPrepared = $this->comments_helper->mark_comments_user_can_like($comments, $commentsLikedByUser);

        $likes = $this->like_model->get_likes_count($id, 'news');

        $responseData = [
            'news_item' => $news,
            'comments' => $commentsPrepared,
            'likes' => $likes,
        ];

        $this->response($responseData);
    }
}