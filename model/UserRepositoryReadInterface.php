<?php

namespace model;

interface UserRepositoryReadInterface {

  public static function fetchByUsername(\PDO $db, string $username);
}