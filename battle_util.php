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
  $response_array["my_board"] = $my_board;
  $response_array["enemy_board"] = $enemy_board;
  echo json_encode($response_array);
  die();
}

function set_new_move()
{
  global $login;
  global $id;
  global $db;

  $time_now = date($time_format, time());
  $query = "update strikes_new_moves set new_move = $time_now where id = $id";
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

//temp


function start_battle(){
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
}

function strike(){
  global $login;
  global $id;
  global $enemy_login;
  global $time_format;
  global $db;

  $time_now = date($time_format, time());
  $query = "update strikes_new_moves set last_strike = $time_now where id = $id";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();

  $query = "insert into curr_move values ($id, $enemy_login)";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();

  if (mt_rand(0, 6) == 2)
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

  $str = '';
  for ($i = 0; $i < 100; $i++)
    $str = $str . '0';


  $query = "update battle_positions set board = '$str' where id = $id and login = '$login'";
  $statement = $db->prepare($query);
  $statement->execute();
  $statement->closeCursor();
}
