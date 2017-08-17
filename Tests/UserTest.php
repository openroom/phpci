<?php

class UserTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    public function setUp()
    {
        $this->pdo = new PDO($GLOBALS['db_dsn'], $GLOBALS['db_username'], $GLOBALS['db_password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->query("DROP TABLE IF EXISTS users CASCADE");
        $createTable = "CREATE TABLE users  (
  id               SERIAL PRIMARY KEY,
  username         TEXT                        NOT NULL UNIQUE,
  display_name     TEXT,
  password         TEXT                        NOT NULL,
  email            TEXT                        NOT NULL,
  last_login       TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT (now()),
  is_active        BOOLEAN                     NOT NULL DEFAULT FALSE,
  is_administrator BOOLEAN                     NOT NULL DEFAULT FALSE,
  is_reporter      BOOLEAN                     NOT NULL DEFAULT FALSE,
  is_banned        BOOLEAN                     NOT NULL DEFAULT FALSE
);";
        $this->pdo->query($createTable);
    }

    public function tearDown()
    {
        if (!$this->pdo) {
            return;
        }
        $this->pdo->query("DROP TABLE users");
    }

    public function testAddAdminUser()
    {
        $tableName = 'users';
        $hashedPassword = \model\User::hashPassword('hunter2');
        $admin = \model\User::create()
            ->setUsername("hunter")
            ->setPassword($hashedPassword)
            ->setDisplayName("Gunter Adams")
            ->setEmail("hikingfan@gmail.com")
            ->setIsActive(true)
            ->setIsBanned(false)
            ->setIsReporter(true)
            ->setIsAdministrator(true);
        \model\User::addUser($this->pdo, $admin);
        $adminEmail = \model\User::fetchByUsername($this->pdo, "hunter")->getEmail();
        $this->assertEquals('hikingfan@gmail.com', $adminEmail);
    }
}