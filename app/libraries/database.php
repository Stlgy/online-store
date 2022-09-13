<?php

class Database {
    private $host = 'localhost';
    private $user = 'ifreshhost15_estagio';
    private $pass = 'agosto2022#';
    private $dbname ='ifreshhost15_estagio';

    //PDO object
    private $dbh; //Assign pdo object
    private $stmt;//Hold querie to pdo
    private $error;// If assignment to pdo object not successfull error will be shown

    public function __construct()//inicialize pdo object with construct method
    {
        //set DSN
        //Concatenate class properties
        $dsn = 'mysql:host='.$this->host.';dbname='.$this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,//Always check for existing PDO connection BEFORE CREATING 1
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION// Way of handling errors
        );
        //Create PDO instance
        try{
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }catch(PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }
    //Prepare statement with query
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);//dbh will pdo if success try catch
    }
    //Binding values to prepared the statement using named parameters
    //Target by name 
    public function bind($param, $value, $type = null){
        if(is_null($type)){//if null figure data type
            switch(true){
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                $type = PDO::PARAM_STR;  //Most likely to be a string
            }
        }
        //binding values to the prepare stmt
        $this->stmt->bindValue($param, $value, $type);
    }
    //Once prepared stmt finishes beeing prepared we are able to run the querie
    //Executing the prepared stmt
    public function execute(){
        return $this->stmt->execute();
    }
    //Returning multiple records
    //Returns an array of objects
    public function resultSet(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }
    //Returning single record
    //Returns single object, the 1st that match the querie
    public function single(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }
    //Get row count
    //Method returns how many rows match the querie executed for PDO
    public function rowCount(){
        return $this->stmt->rowCount();
    }
}
