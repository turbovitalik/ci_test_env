<?php

class News_Comment_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->class_table = 'news_comments';
        $this->db = $this->getSparrow();
    }

    public function insert_comment($data)
    {
        $this->db->from($this->class_table)
            ->insert($data)
            ->execute();
    }

    public function get_news_comments($newsId)
    {
        $sql = "
            select l.item_id, nc.comment, nc.user_id, nc.id as comment_id, count(item_id) as likes_count
            from news_comments as nc
            left join likes as l on nc.id = l.item_id and like_type = 'comment'
            where news_id = $newsId
            group by l.item_id, nc.comment, nc.id, nc.user_id;
        ";

        $comments = $this->db->sql($sql)->many();

        return $comments;
    }
}