<?php

class PersonTest extends PHPUnit_Framework_TestCase {

  /**
   * @var PDO
   */
  private $pdo;

  public function setUp() {
    $this->pdo = new PDO($GLOBALS['db_dsn'], $GLOBALS['db_username'], $GLOBALS['db_password']);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->pdo->query("CREATE TABLE person (what VARCHAR(50) NOT NULL)");
  }

  public function tearDown() {
    if (!$this->pdo) {
      return;
    }
    $this->pdo->query("DROP TABLE person");
  }

  public function testHelloWorld() {
    $person = new Person($this->pdo);

    $this->assertEquals('Hello World', $person->hello());
  }

  public function testHello() {
    $person = new Person($this->pdo);

    $this->assertEquals('Hello Bar', $person->hello('Bar'));
  }

  public function testWhat() {
    $person = new Person($this->pdo);

    $this->assertFalse($person->what());

    $person->hello('Bar');

    $this->assertEquals('Bar', $person->what());
  }
}

