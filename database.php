<?php
    $dsn = 'mysql:host=localhost;dbname=seabattle';
    $username = 'root';
    $password = '837083';

    try {
        $db = new PDO($dsn, $username, $password);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        exit();
    }

    function check_login_password($login, $password)
    {
      global $db;
      $query = "SELECT count(*) as count FROM logins WHERE login = :login and password = :password";
      $statement = $db->prepare($query);
      $statement->bindValue(':login', $login);
      $statement->bindValue(':password', $password);
      $statement->execute();

      $input = $statement->fetch();
      $statement->closeCursor();

      return ($input['count'] > 0);
    }
?>
