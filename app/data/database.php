<?php
namespace App\Data;
use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try
        {
            $this->pdo = new PDO('mysql:host=localhost;dbname=tienda_database;charset=utf8', 'root', '', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->pdo->exec("SET CHARACTER SET utf8");
        } 
        catch (PDOException $e)
        {
            print "Error!: " . $e->getMessage();
            die();
        }
    }

    public static function getInstance()
    {
        if(self::$instance === null) 
        {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}