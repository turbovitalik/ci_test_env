<?php

class Comments_Helper
{
    public function mark_comments_user_can_like($allComments, $userLikes)
    {
        $commentIdsLikedByUser = [];
        foreach ($userLikes as $like) {
            $commentIdsLikedByUser[] = $like['item_id'];
        }

        $markedComments = [];
        foreach ($allComments as $comment) {
            $comment['isLiked'] = in_array($comment['comment_id'], $commentIdsLikedByUser) ? 1 : 0;
            $markedComments[] = $comment;
        }

        return $markedComments;
    }
}