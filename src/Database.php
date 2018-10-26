<?php
require_once "../conf.php";

class Database
{
    private $host = DB_HOST;
    private $db = DB_NAME;
    private $user = DB_USER;
    private $pass = DB_PASS;

    //Protected for extended classes
    protected $connection = null;

    private function initConnection()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->connection = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function SelectQuery($q){
        //Lazy connection initialization
        if($this->connection == null){
            $this->initConnection();
        }

        $ret = array();
        try{
            $result = $this->connection->query($q);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        //Loop results and save to array for return
        while ($row = $result->fetch())
        {
            $ret[] = $row;
        }
        return $ret;
    }

    /**
     * @param $q
     * @return null
     */
    public function Query($q){
        //Lazy connection initialization
        if($this->connection == null){
            $this->initConnection();
        }

        $result = null;
        try{
            $result = $this->connection->query($q);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
        return $result;
    }
}