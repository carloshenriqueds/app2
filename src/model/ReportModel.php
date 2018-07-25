<?php

namespace App\Model;

use App\Model\DB;

class ReportModel
{
        
    /**
     * DB constructor.
     */
    public function __construct(){}

    /**
     * Get data from live
     * @return array $response
     */
    public function selectCounters()
    {
        $query = "SELECT authorName, count(*) as counter from comment group by authorName ORDER BY counter DESC limit 20";
        $db = new DB();
        $result = $db->executeGet($query);
        $response=[];
        while ($aux_query = $result->fetch_assoc()){
            $response[]=[
                "name" => $aux_query["authorName"],
                "counter" => $aux_query["counter"]
            ];
            
        }
        return $response;
    }

    /**
     * Get data from live
     * @param string $author
     * @return array $response
     */
    public function searchAuthor($author)
    {
        $query = "SELECT authorName, comment from comment where authorName like '%$author%' limit 50";
        $db = new DB();
        $result = $db->executeGet($query);
        $response=[];
        while ($aux_query = $result->fetch_assoc()){
            $response[]=[
                "name" => $aux_query["authorName"],
                "comment" => $aux_query["comment"]
            ];
            
        }
        return $response;
    }

}