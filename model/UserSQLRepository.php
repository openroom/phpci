<?php

namespace model;

class UserSQLRepository implements \model\UserRepositoryReadInterface, \model\UserRepositoryWriteInterface {

  public static function fetchByUsername(\PDO $db, string $username): User {
    $req = $db->prepare('SELECT id, username, display_name, password, email, last_login, is_active, is_administrator, is_reporter, is_banned FROM users WHERE username = :username');
    $req->execute(['username' => $username]);
    $user = $req->fetch();
    return User::create()
      ->setId($user['id'])
      ->setUsername($user['username'])
      ->setDisplayName($user['display_name'])
      ->setPassword($user['password'])
      ->setEmail($user['email'])
      ->setLastLogin($user['last_login'])
      ->setIsActive($user['is_active'])
      ->setIsAdministrator($user['is_administrator'])
      ->setIsReporter($user['is_reporter'])
      ->setIsBanned($user['is_banned']);
  }

  public static function saveUser(\PDO $db, \model\User $user) {
    $username = $user->getUsername();
    $displayName = $user->getDisplayName();
    $password = $user->getPassword();
    $email = $user->getEmail();
    $req = $db->prepare('INSERT INTO users (username, display_name, password, email) VALUES (:username, :display_name, :password, :email)');
    $req->bindParam(':username', $username, \PDO::PARAM_STR, 255);
    $req->bindParam(':display_name', $displayName, \PDO::PARAM_STR, 255);
    $req->bindParam(':password', $password, \PDO::PARAM_STR, 255);
    $req->bindParam(':email', $email, \PDO::PARAM_STR, 255);
    $req->execute();
    $req = NULL;
  }
}
