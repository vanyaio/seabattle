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


    //move to util???
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
    //get last_battle
    function last_battle_is_over($login)
    {
        global $db;
        $last_battle_query = "SELECT MAX(id) AS last from login_battles where login = :login";

        $statement = $db->prepare($last_battle_query);
        $statement->bindValue(':login', $login);
        $statement->execute();

        $last_battle = ($statement->fetch())['last'];
        if ($last_battle == null)
          return true;
        $statement->closeCursor();

        $status_query = "SELECT winner, left_by_both from battle_status where id = :id";
        $statement = $db->prepare($status_query);
        $statement->bindValue(':id', $last_battle);
        $statement->execute();

        $status = $statement->fetch();
        $statement->closeCursor();


        if ($status['winner'] != null || $status['left_by_both'] == true)
          return true;

        return false;
    }
?>
<!--










aa















bb -->
