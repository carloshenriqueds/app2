<?php

namespace App\Model;

class Db
{
    /**
     * @var string
     */
    const HOSTNAME = '';
    /**
     * @var string
     */
    const USERNAME = '';
    /**
     * @var string
     */
    const PASSWORD = '';
    /**
     * @var string
     */
    const DBNAME = '';

    /**
     * @var string
     */
    private $db;

    /**
     * DB constructor.
     */
    public function __construct()
    {
        $this->db = new \mysqli(self::HOSTNAME,self::USERNAME,self::PASSWORD,self::DBNAME) or die("Unable to Connect DB");
    }

    /**
     * Exeute DB's query
     * @method execute
     * @param string $query
     * @return string
     */
    public function execute($query){
        return $this->db->query($query);
    }

    
    /**
     * Exeute DB's query
     * @method execute
     * @param string $query
     * @return string
     */
    public function executeGet($query){
        $data = $this->db->query($query);
        return $data;
    }

}