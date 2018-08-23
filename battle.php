<?php

session_start();
require_once('database.php');
require_once('util.php');
require_once("battle_util.php");
//kkekefdsfdsf;
if (check_login_password($_SESSION['login'], $_SESSION['password']) == false)
{
  header("Location: index.php");
  die();
}

if (last_battle_is_over($_SESSION['login'])){
  if (isset($_POST['start_battle'])) { //kinda temprory
    start_battle();
    require_once("battle_page.php");
    die();
  }
  else{
    header("Location: index.php");
    die();
  }
}

if (!isset($_POST['battle_page_loaded']))
{
  require_once("battle_page.php");
  die();
}

$login = $_SESSION['login'];
$id = get_battle_id($login);
$enemy_login = get_enemy_login($login);

$login_isset = true;
$enemy_isset = true;
$my_board = get_login_board($login, $id);
$enemy_board = get_login_board($enemy_login, $id);
if (get_login_board($login, $id) == null)
  $login_isset = false;
if (get_login_board($enemy_login, $id) == null)
  $enemy_isset = false;

$both_set = false;
if ($login_isset && $enemy_isset)
    $both_set = true;

$battle_start = battle_start($id);

$new_move = get_ls_nm($id)["new_move"];
$last_strike = get_ls_nm($id)["last_strike"];
if ($new_move != false)
  $new_move = strtotime($new_move);
if ($last_strike != false)
  $last_strike = strtotime($last_strike);

$curr_move = get_move($id);



$set_time = 20;
$req_time = 5;
$accept_time = 6;
$move_time = 20;
//need to make func set_left_by_both
if (!$both_set){
  if (!$login_isset && (time() > ($battle_start + $set_time))) {
      if (time() > ($battle_start + $set_time + $req_time)) {
        //left_by_both = true;
        set_left_by_both();
        send_response(array("left_by_both" => true));
      }
      else {
        //in_progress;
        send_response(array("in_progress" => true));
      }
  }

  if ($login_isset && (time() > ($battle_start + $set_time + $req_time))){
    //left_by_both = true;
    set_left_by_both();
    send_response(array("left_by_both" => true));
  }
}
else{
  if ($new_move == false){
    if (time() > ($both_set_time + $accept_time + $req_time)){
      //left_by_both = true;
      set_left_by_both();
      send_response(array("left_by_both" => true));
    }
    if ($curr_move == $login && (time() > ($both_set_time + $accept_time)))
    {
      //in_progress;
      send_response(array("in_progress" => true));
    }
  }
  else{
    if (($last_strike == false) || ($last_strike != false && ($new_move > $last_strike)))
    {
      if ($curr_move == $login)
      {
        if (time() > ($new_move + $move_time + $req_time)){
          //left_by_both;
          set_left_by_both();
          send_response(array("left_by_both" => true));
        }
        else
          //in_progress;
          send_response(array("in_progress" => true));
      }
      else
      {
        if (time() > ($new_step_time + $move_time + $req_time)){
          //left_by_both;
          set_left_by_both();
          send_response(array("left_by_both" => true));
        }
      }
    }

    if ($last_strike != false && ($last_strike > $new_move))
    {
      if (time() > ($last_strike + $accept_time + $req_time)){
        //left_by_both;
        set_left_by_both();
        send_response(array("left_by_both" => true));
      }
      if ($curr_move == $login)
      {
        if (time() > ($last_strike + $accept_time))
          //in_progress;
          send_response(array("in_progress" => true));
      }
    }
  }
}




if ($both_set) {
  if ($new_move == false)
  {
    if ($curr_move == $login)
    {
      set_new_move();
      // status;
      // move, your, positions, timer:3;
      $timer = $new_move + $move_time - time();
      send_response(array("stage" => "move", "my_move" => true, "timer" => $timer));
    }
    else
    {
      if (time() > ($both_set + $accept_time))
      {
        set_winner();
        //status;
        send_response(array("winner" => $login));
      }
      else
      {
        // status;
        // 2, 0, poses, 2a
        $timer = $both_set + $accept_time - time();
        send_response(array("stage" => "accept new move", "my_move" => false, "timer" => $timer));
      }
    }
  }
  else
  {
    if (($last_strike != false) && ($last_strike > $new_move))
    {
      if ($curr_move == $login)
      {
        set_new_move();
        // status ;
        // 3, 1, poses, 3
        $timer = $new_move + $move_time - time();
        send_response(array("stage" => "move", "my_move" => true, "timer" => $timer));
      }
      else
      {
        if (time() > ($last_strike + $accept_time))
        {
          set_winner();
          //status;
          send_response(array("winner" => $login));
        }
      }
    }

    if ($last_strike == false || ($new_move > $last_strike))
    {
      if ($curr_move == $login)
      {
        //strike();
        //if strike finish game?
        // status;
        // 2, 0, poses, 2b
        if (strike())
        {
          set_winner();
          //status;
          send_response(array("winner" => $login));
        }
        else
        {
          $timer = $last_strike + $accept_time - time();
          send_response(array("stage" => "accept new move", "my_move" => false, "timer" => $timer));
        }
      }
      else
      {
        if (time() > ($new_move + $move_time))
        {
          set_winner();
          //status;
          send_response(array("winner" => $login));
        }
      }
    }
  }
}
else {
  if ($login_isset)
  {
    if (time() > ($battle_start + $set_time))
    {
      set_winner();
      send_response(array("winner" => $login));
    }
    // status;
    // 1, null, poses, 1
    $timer = $battle_start + $set_time - time();
    send_response(array("stage" => "set position", "my_move" => false, "timer" => $timer));
  }
  else
  {
    set_position($_POST["set_position"]);
    $timer = $battle_start + $set_time - time();
    $my_board = get_login_board($login, $id);
    send_response(array("stage" => "set position", "my_move" => false, "timer" => $timer));
  }
}
