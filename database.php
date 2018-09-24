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
    
    $db_obj = new Database($dsn, $username, $password);
    class Database{
        public function __construct($dsn, $username, $password) {
            try {
                $this->db = new PDO($dsn, $username, $password);
            } catch (PDOException $e) {
                $error_message = $e->getMessage();
                exit();
            }
        }
        
        private function generateQuery($query, $bind_vals){
            $statement = $this->db->prepare($query);
            
            for ($i = 0; $i < count($bind_vals); $i += 2){
                $statement->bindValue($bind_vals[$i], $bind_vals[$i + 1]);
            }     

            $statement->execute();
            return $statement->fetch();
        }
        
        public function select($cols, $from, $where, ...$bind_vals)
        {
            $query = "SELECT " . $cols . " FROM " . $from . " WHERE " . $where;  
            return $this->generateQuery($query, $bind_vals);
        }
        
        public function insert($into, $vals_str, $vals, ...$bind_vals){
            $query = "INSERT INTO " . $into . " " . $vals_str . " VALUES " . $vals;
            return $this->generateQuery($query, $bind_vals);
        }
        
        public function update($table, $set, $where, ...$bind_vals){
            $query = "UPDATE " . $table . " SET " . $set . " WHERE " . $where;
            return $this->generateQuery($query, $bind_vals);
        }

        public function delete($table, $where, ...$bind_vals){
            $query = "DELETE FROM " . $table . " WHERE " .$where;
            return $this->generateQuery($query, $bind_vals);
        }
        public $db;
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
    // function last_battle_is_over($login)
    // {
    //     global $db;
    //     $last_battle_query = "SELECT MAX(id) AS last from login_battles where login = :login";
    //
    //     $statement = $db->prepare($last_battle_query);
    //     $statement->bindValue(':login', $login);
    //     $statement->execute();
    //
    //     $last_battle = ($statement->fetch())['last'];
    //     if ($last_battle == null)
    //       return true;
    //     $statement->closeCursor();
    //
    //     $status_query = "SELECT winner, left_by_both from battle_status where id = :id";
    //     $statement = $db->prepare($status_query);
    //     $statement->bindValue(':id', $last_battle);
    //     $statement->execute();
    //
    //     $status = $statement->fetch();
    //     $statement->closeCursor();
    //
    //
    //     if ($status['winner'] != null || $status['left_by_both'] == true)
    //       return true;
    //
    //     return false;
    // }
