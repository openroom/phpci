<?php

class HelloWorldTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var PDO
     */
    private $pdo;

    public function setUp()
    {
        $this->pdo = new PDO($GLOBALS['db_dsn'], $GLOBALS['db_username'], $GLOBALS['db_password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->query("CREATE TABLE hello (what VARCHAR(50) NOT NULL)");
    }

    public function tearDown()
    {
        if (!$this->pdo) {
            return;
        }
        $this->pdo->query("DROP TABLE hello");
    }

    public function testHelloWorld()
    {
        $helloWorld = new \model\HelloWorld($this->pdo);

        $this->assertEquals('Hello World', $helloWorld->hello());
    }

    public function testHello()
    {
        $helloWorld = new \model\HelloWorld($this->pdo);

        $this->assertEquals('Hello Bar', $helloWorld->hello('Bar'));
    }

    public function testWhat()
    {
        $helloWorld = new \model\HelloWorld($this->pdo);

        $this->assertFalse($helloWorld->what());

        $helloWorld->hello('Bar');

        $this->assertEquals('Bar', $helloWorld->what());
    }

    public function testDropAndCreateDuck()
    {
        $db = $this->pdo;
        $tableName = 'duck';
        $createTable = "create table {$tableName} (id SERIAL PRIMARY KEY,username varchar(50) NOT NULL UNIQUE)";
        $populateTable = "INSERT INTO {$tableName} (username) VALUES ('admin')";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
    }

    public function testDropAndCreateUsers()
    {
        $db = $this->pdo;
        $tableName = 'users';
        $hashedPassword = \model\User::hashPassword('hunter2');
        $createTable = "CREATE TABLE {$tableName}  (
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
        $populateTableAdmin = "INSERT INTO {$tableName}  (username, password, email, is_active, is_administrator) 
VALUES ('admin', '{$hashedPassword}', 'hikingfan@gmail.com', TRUE, TRUE);";
        $populateTableReporter = "INSERT INTO {$tableName}  (username, password, email, is_active, is_reporter) 
VALUES ('reporter', '{$hashedPassword}', 'hikingfan+reporter@gmail.com', TRUE, TRUE);";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTableAdmin);
        $this->executeStatement($db, $populateTableReporter);
    }

    public function testDropAndCreateSettings()
    {
        $db = $this->pdo;
        $tableName = 'settings';
        $createTable = "CREATE TABLE {$tableName}  (
  id    SERIAL PRIMARY KEY,
  name  TEXT UNIQUE,
  value TEXT
);";
        $populateTable = "INSERT INTO {$tableName}  (name, value) VALUES 
('login_method', 'normal'),
('systemid', '80zhh73n5'),
('theme', 'default'),
('instance_name', 'Openroom Demo'),
('policies', 'Rosenthal Library usually has several rooms available to students for group study on a ' ||
               'first-come, first-serve basis. These rooms are available to currently registered Queens College ' ||
               'students only.\r\n\r\nImmediate use of a Group Study Room is made by presenting your valid Queens ' ||
               'College ID at the Circulation Desk (located on Level 3 of the Library). If available, a room will ' ||
               'be assigned to you for one 2-hour time block. If the room is in use a hold may be placed to secure ' ||
               'the next available time slot. Room use, like book use, is assigned to your record in our automated ' ||
               'circulation system. When a room is assigned to you, you will be handed a wooden block upon which ' ||
               'the room number and policies governing Group Study Rooms is adhered. Upon completing your use of ' ||
               'the room, the wooden block is to be returned to the Circulation Desk and the assignment of the room ' ||
               'to your record will be released.\r\n\r\nShould you wish to extend the use of the room you are ' ||
               'required to return to the Circulation desk with your ID and the wooden block at the end of the 2 ' ||
               'hours. The room will then be reassigned to you provided there are no other users awaiting use of ' ||
               'the room.'),
('time_format', 'g:i a');";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
    }

    public function testDropAndCreateGroups()
    {
        $db = $this->pdo;
        $tableName = 'groups';
        $createTable = "create table {$tableName} (id SERIAL PRIMARY KEY, name TEXT NOT NULL)";
        $populateTable = "INSERT INTO {$tableName} (name) VALUES ('apple')";
        $populateTable1 = "INSERT INTO {$tableName} (name) VALUES ('ball')";
        $populateTable2 = "INSERT INTO {$tableName} (name) VALUES ('cat')";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
        $this->executeStatement($db, $populateTable1);
        $this->executeStatement($db, $populateTable2);
    }

    public function testDropAndCreateRooms()
    {
        $db = $this->pdo;
        $tableName = 'rooms';
        $createTable = "create table {$tableName} (
  id          SERIAL PRIMARY KEY,
  name        TEXT,
  position    INTEGER,
  capacity    INTEGER,
  groupid     INTEGER REFERENCES Groups (id),
  description TEXT
);";
        $populateTable = "INSERT INTO {$tableName} (name, position, capacity, groupid, description)
VALUES ('방 101', 1, 8, 1, '이것은 시험이다.')";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
    }

    public function testDropAndCreateReservations()
    {
        $db = $this->pdo;
        $tableName = 'reservations';
        $createTable = "create table {$tableName} (
  id                   SERIAL PRIMARY KEY,
  start_time           TIMESTAMP NOT NULL,
  end_time             TIMESTAMP NOT NULL,
  room_id              INTEGER   NOT NULL REFERENCES Rooms (id),
  user_id              INTEGER   NOT NULL REFERENCES Users (id),
  number_in_group      INTEGER   NOT NULL DEFAULT 1,
  time_of_request      TIMESTAMP NOT NULL DEFAULT (now()),
  time_of_cancellation TIMESTAMP          DEFAULT NULL
);";
        $populateTable = "INSERT INTO {$tableName} (start_time, end_time, room_id, user_id) 
VALUES ('2017-03-26 11:30:00.000000', '2017-03-26 11:55:00.000000', 1, 1);";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
    }

    public function testDropAndCreateHours()
    {
        $db = $this->pdo;
        $tableName = 'hours';
        $createTable = "create table {$tableName} (
  id          SERIAL PRIMARY KEY,
  room_id     INTEGER   NOT NULL REFERENCES Rooms (id),
  day_of_week SMALLINT  NOT NULL,
  start_time  TIMESTAMP NOT NULL,
  end_time    TIMESTAMP NOT NULL
);";
        $populateTable = "INSERT INTO {$tableName} (room_id, day_of_week, start_time, end_time) VALUES 
  (1, 1, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, 2, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, 3, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, 4, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, 5, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, 6, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, 7, '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000');";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
    }

    public function testDropAndCreateSpecialHours()
    {
        $db = $this->pdo;
        $tableName = 'specialhours';
        $createTable = "create table {$tableName} (
  id         SERIAL PRIMARY KEY,
  room_id    INTEGER   NOT NULL REFERENCES Rooms (id),
  from_range TIMESTAMP NOT NULL,
  to_range   TIMESTAMP NOT NULL,
  start_time TIMESTAMP NOT NULL,
  end_time   TIMESTAMP NOT NULL
);";
        $populateTable = "INSERT INTO {$tableName} (room_id, from_range, to_range, start_time, end_time) VALUES 
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000'),
  (1, '2016-10-10 04:00:00', '2016-10-10 04:00:00', '2017-03-26 11:30:00.000000', '2017-03-26 13:30:00.000000');";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
    }

    public function testDropAndCreateOptionalFields()
    {
        $db = $this->pdo;
        $tableName = 'optionalfields';
        $createTable = "create table {$tableName} (
  id          SERIAL PRIMARY KEY,
  name        TEXT    NOT NULL,
  form_name   TEXT    NOT NULL,
  type        TEXT    NOT NULL,
  choices     JSON    NOT NULL,
  position    INTEGER NOT NULL,
  question    TEXT    NOT NULL,
  is_private  BOOLEAN NOT NULL DEFAULT FALSE,
  is_required BOOLEAN NOT NULL DEFAULT FALSE
);";
        $populateTable = "INSERT INTO {$tableName} 
  (name, form_name, type, choices, position, question, is_private, is_required) 
  VALUES
  ('campus affiliation', 'campus affiliation form', 1, '{
    \"0\": \"Undergraduate\",
    \"1\": \"Graduate\",
    \"2\": \"Faculty / Staff\"
  }', 1,'What is your Campus Affiliation?', FALSE, TRUE
  );";
        $populateTable2 = "INSERT INTO {$tableName}
    (name, form_name, type, choices, position, question, is_private, is_required) VALUES
    ('random question name', 'random question form name', 1, '{
    \"0\": \"蘇步青\",
    \"1\": \"復旦大學\",
    \"2\": \"上海浦\",
    \"3\": \"也係世界跟到新加坡同香港後面嗰世界第三大貨櫃港\",
    \"4\": \"Cómo estás hoy?\"
  }', 1,
   '\"¿Cuál es su afiliación en el campus ?\"', FALSE, TRUE
  );";
        $this->dropTable($db, $tableName);
        $this->executeStatement($db, $createTable);
        $this->executeStatement($db, $populateTable);
        $this->executeStatement($db, $populateTable2);
    }

    public function testDropTable(\PDO $db, string $tableName)
    {
        $statement = "DROP TABLE IF EXISTS {$tableName} CASCADE";
        $this->executeStatement($db, $statement);
    }

    public function testexecuteStatement(\PDO $db, string $statement)
    {
        try {
            $req = $db->prepare("{$statement}");
            $req->execute();
            echo "executed statement {$statement}";
            echo "<br />";
            echo PHP_EOL;
            echo PHP_EOL;
            echo PHP_EOL;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
}
