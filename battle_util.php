<?php

require_once("database.php");

$time_format ='Y-m-d H:i:s';

function get_battle_id($login){
  global $db;

  $last_battle_query = "SELECT MAX(id) AS last from battle_logins where login0 = :login or login1 = :login";
  $statement = $db->prepare($last_battle_query);
  $statement->bindValue(':login', $login);
  $statement->execute();

  return ($statement->fetch())['last'];
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

function last_battle_is_over($login){
  $id = get_battle_id($login);

  if ($id == false)
    return true;

  $status = get_battle_status($id);
  if (($status['winner'] != null) || ($status['left_by_both'] === '1'))
    return true;

  return false;
}

function get_enemy_login($login){
  global $db;

  $battle_id = get_battle_id($login);

  if ($battle_id == null){
    //code
  }

  // $query = "SELECT login from login_battles where login <> :login and id = :id";
  // $statement = $db->prepare($query);
  // $statement->bindValue(':login', $login);
  // $statement->bindValue(':id', $battle_id);
  // $statement->execute();

  $query = "SELECT login0, login1 from battle_logins where id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':id', $battle_id);
  $statement->execute();

  $res = $statement->fetch();
  $enemy = ($res['login0'] == $login) ? $res['login1'] : $res['login0'];
  return $enemy;
}

function get_login_board($login, $id)//*
{
  global $db;

  $query = "SELECT board from battle_positions where login = :login and id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':login', $login);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return $statement->fetch()['board'];
}
function get_positions_str($login, $id)//*
{
  global $db;

  $query = "SELECT position from positions_str where login = :login and id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':login', $login);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return $statement->fetch()['position'];
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

function get_move($id){
  global $db;

  $query = "SELECT login from curr_move where id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return ($statement->fetch())['login'];
}

function add_to_date($date, $add_secs){
  global $time_format;
  return (strtotime($date->format('Y-m-d H:i:s') + $add_secs));
}

function battle_start($id){
  global $db;

  $query = "SELECT start from battle_start where id = :id";
  $statement = $db->prepare($query);
  $statement->bindValue(':id', $id);
  $statement->execute();

  return strtotime(($statement->fetch())['start']);
}

function send_response($response_array){
  global $my_board;
  global $enemy_board;
  global $login;
  global $enemy_login;
  global $id;
  //$response_array["my_board"] = $my_board;//get my board()
  $response_array["my_board"] = get_login_board($login, $id);
  //$response_array["enemy_board"] = $enemy_board;//same
  $response_array["enemy_board"] = get_login_board($enemy_login, $id);
  echo json_encode($response_array);
  die();
}

function set_new_move()
{
  global $login;
  global $id;
  global $db;
  global $time_format;

  $time_now = date($time_format, time());
  $query = "update strikes_new_moves set new_move = '$time_now' where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();
}

function set_winner(){
  global $login;
  global $id;
  global $db;

  $query = "update battle_status set winner = '$login' where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();
}

function set_left_by_both(){
  global $login;
  global $id;
  global $db;

  $query = "update battle_status set left_by_both = '1' where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();
}

function get_both_set_time(){
  global $login;
  global $id;
  global $db;

  $query = "select both_set_time from both_set_time where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();

  $res = ($statement->fetch())['both_set_time'];
  return strtotime($res);
}

function set_both_set_time(){
  global $login;
  global $id;
  global $db;
  global $time_format;

  $time_now = date($time_format, time());
  $query = "update both_set_time set both_set_time = '$time_now' where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();
}
//temp


function start_battle(){//ADD POSITIONS_STR
  global $db;
  global $time_format;

  $query = 'insert into battle_logins (login0, login1) values ("ya0", "ya1")';
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();

  $id = get_battle_id('ya0');

  $query0 = "insert into battle_positions (id, login) values ($id, 'ya0')";
  $statement0 = $db->prepare($query0);
  $res0 = $statement0->execute();
  $statement0->closeCursor();

  $query1 = "insert into battle_positions (id, login) values ($id, 'ya1')";
  $statement1 = $db->prepare($query1);
  $res1 = $statement1->execute();
  $statement1->closeCursor();
  
  $query_0 = "insert into positions_str (id, login) values ($id, 'ya0')";
  $statement_0 = $db->prepare($query_0);
  $res_0 = $statement_0->execute();
  $statement_0->closeCursor();

  $query_1 = "insert into positions_str (id, login) values ($id, 'ya1')";
  $statement_1 = $db->prepare($query_1);
  $res_1 = $statement_1->execute();
  $statement_1->closeCursor();

  $battle_start = date($time_format, time());
  $query2 = "insert into battle_start values ($id, '$battle_start')";
  $statement2 = $db->prepare($query2);
  $res2 = $statement2->execute();
  $statement2->closeCursor();

  $query3 = "insert into battle_status (id) values($id)";
  $statement3 = $db->prepare($query3);
  $statement3->execute();
  $statement3->closeCursor();

  $query4 = "insert into curr_move values ($id, 'ya0')";
  $statement4 = $db->prepare($query4);
  $statement4->execute();
  $statement4->closeCursor();

  $query5 = "insert into strikes_new_moves (id) values ($id)";
  $statement5 = $db->prepare($query5);
  $statement5->execute();
  $statement5->closeCursor();

  $query6 = "insert into both_set_time (id) values ($id)";
  $statement6 = $db->prepare($query6);
  $statement6->execute();
  $statement6->closeCursor();
}

function strike(){
  global $login;
  global $id;
  global $enemy_login;
  global $time_format;
  global $db;

  $time_now = date($time_format, time());
  $query = "update strikes_new_moves set last_strike = '$time_now' where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();

  $query = "update curr_move set login = '$enemy_login' where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();

  if (true)//)
  {
    set_winner();
    return true;
  }

  return false;
}

function set_position($position){
  global $login;
  global $id;
  global $db;

  if (!isset($position))
    return false;

  $str = '';
  for ($i = 0; $i < 100; $i++)
    $str = $str . '0';


  $query = "update battle_positions set board = '$str' where id = $id and login = '$login'";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();

  return true;
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
