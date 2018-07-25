<?php

namespace App\Model;

use App\Model\DB;

class CommentModel
{
        
    /**
     * DB constructor.
     */
    public function __construct(){}

    /**
     * Update/inser comment
     * @param string $comment
     * @param string $authorName
     * @param string $url
     * @param string $idGoogle
     * @param string $data
     * @param string $userId
     * @return void
     */
    public function updateComment($comment,$authorName,$url,$idGoogle,$data,$userId)
    {
        $query = "INSERT INTO comment (comment,authorName,authorUrlProfile,idGoogleComment,dataComment,userSessionId) 
                  VALUES ('$comment', '$authorName','$url','$idGoogle','$data','$userId') 
                  ON DUPLICATE KEY UPDATE authorUrlProfile='$url'";
        $db = new DB();
        $db->execute($query);

    }

}