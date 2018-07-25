<?php

namespace App\Model;

use App\Model\DB;

class SettingsModel
{
    
    /**
     * DB constructor.
     */
    public function __construct(){}


    /**
     * Update settings
     * @param string $chatId
     * @param string $lastDate
     * @return void
     */
    public function updateSettings($chatId, $lastDate)
    {
        $query = "INSERT INTO settings (chatId,lastDateComment) VALUES ('$chatId', '$lastDate') ON DUPLICATE KEY UPDATE lastDateComment='$lastDate'";
        $db = new DB();
        $db->execute($query);

    }

    /**
     * Get date from last comment
     * @param string $chatId
     * @return $array
     */
    public function getLastCommentDate($chatId)
    {
        $query = "SELECT lastDateComment from settings where chatId='$chatId'";
        $db = new DB();
        return $db->executeGet($query)->fetch_assoc();
    }

}