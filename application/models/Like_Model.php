<?php

class Like_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->class_table = 'likes';
    }

    public function create_like($likeType, $itemId, $userId)
    {
        $db = $this->getSparrow();

        $db->from($this->class_table)
            ->insert([
                'like_type' => $likeType,
                'item_id' => $itemId,
                'user_id' => $userId,
            ])
            ->execute();
    }

    public function get_likes_and_count($itemId, $likeType)
    {
        $db = $this->getSparrow();

        $sql = "
            select c.likes_count, id, user_id, item_id
            from likes, (select count(*) as likes_count from likes where item_id = $itemId and like_type = $likeType) as c
            where item_id = $itemId and like_type = $likeType
        ";

        $likes = $db->sql($sql)->many();

        return $likes;
    }

    public function get_likes_count($itemId, $likeType)
    {
        $db = $this->getSparrow();

        $likesNumber = $db->from($this->class_table)
            ->where('item_id', $itemId)
            ->where('like_type', $likeType)
            ->count();

        return $likesNumber;
    }

    public function remove_like($likeType, $itemId, $userId)
    {
        $db = $this->getSparrow();

        $db->from($this->class_table)
            ->where('like_type', $likeType)
            ->where('item_id', $itemId)
            ->where('user_id', $userId)
            ->delete()
            ->execute();
    }

    public function is_liked_by_user($userId, $itemId, $likeType)
    {
        $db = $this->getSparrow();

        $liked = $db->from($this->class_table)
            ->where('like_type', $likeType)
            ->where('item_id', $itemId)
            ->where('user_id', $userId)
            ->one();

        return $liked;
    }

    public function get_liked_by_user($userId, $newsId, $likeType)
    {
        $db = $this->getSparrow();

        $sql = "
            select *
            from {$this->class_table} as l
            left join news_comments as nc on nc.id = l.item_id
            where like_type = '$likeType' and l.user_id = $userId and nc.news_id = $newsId 
        ";

        $likes = $db->sql($sql)->many();

        return $likes;
    }
}