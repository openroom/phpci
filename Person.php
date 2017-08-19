<?php

class Person
{

    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function hello($what = 'World')
    {
        $sql = "INSERT INTO person VALUES (" . $this->pdo->quote($what) . ")";
        $this->pdo->query($sql);
        return "Hello $what";
    }


    public function what()
    {
        $sql = "SELECT what FROM person";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
}
