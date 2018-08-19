<?php

require("database.php");

function get_battle_id($login){
  global $db;

  $last_battle_query = "SELECT MAX(id) AS last from login_battles where login = :login";
  $statement = $db->prepare($last_battle_query);
  $statement->bindValue(':login', $login);
  $statement->execute();

  return $statement->fetch();
}

function get_battle_status($id){
  global $db;

  if ($id == null)
    return null;

  $query = "SELECT winner, left_by_both from battle_status WHERE id=:id";
  $statement = $db->prepare($query);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return $statement->fetch();
}

function get_enemy_login($login){
  global $db;

  $battle_id = get_battle_id($login);

  if ($battle_id == null){
    //code
  }

  $query = "SELECT login from login_battles where login <> :login and id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':login', $login);
  $statement->bindValue(':id', $battle_id);
  $statement->execute();

  return $statement->fetch()['login'];
}

function get_login_board($login, $id)
{
  global $db;

  $query = "SELECT board from battle_positions where login = :login and id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':login', $login);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return $statement->fetch()['board'];
}

function get_ls_nm($id)
{
  global $db;

  $query = "SELECT last_strike, new_move from strikes_new_moves where id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return $statement->fetch();  
}
// a
//
//
//
//
//
//
//
//
//
//
//
//
//
// //
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
// b
