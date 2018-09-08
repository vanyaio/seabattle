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
    if (isset($_POST['battle_page_loaded']))
      send_response(array("finished" => true));
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

$my_position_str = json_decode(get_positions_str($login, $id));//TRUE ASSOC??

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

$both_set_time = get_both_set_time();

$set_time = 120;
$req_time = 120;
$accept_time = 120;
$move_time = 120;
//need to make func set_left_by_both
if (!$both_set){
  if (!$login_isset && (time() > ($battle_start + $set_time))) {
      if (time() > ($battle_start + $set_time + $req_time)) {
        //left_by_both = true;
        set_left_by_both();
        send_response(array("left_by_both" => true, "line" => 74));
      }
      else {
        //in_progress;
        send_response(array("in_progress" => true));
      }
  }

  if ($login_isset && (time() > ($battle_start + $set_time + $req_time))){
    //left_by_both = true;
    set_left_by_both();
    send_response(array("left_by_both" => true, "line" => 85));
  }
}
else{
  if ($new_move == false){
    if (time() > ($both_set_time + $accept_time + $req_time)){
      //left_by_both = true;
      set_left_by_both();
      send_response(array("left_by_both" => true, "line" => 93));
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
          send_response(array("left_by_both" => true, "line" => 109));
        }
      }
      else
      {
        if (time() > ($new_move + $move_time + $req_time)){
          //left_by_both;
          set_left_by_both();
          send_response(array("left_by_both" => true, "line" => 120));
        }
      }
    }

    if ($last_strike != false && ($last_strike > $new_move))
    {
      if (time() > ($last_strike + $accept_time + $req_time)){
        //left_by_both;
        set_left_by_both();
        send_response(array("left_by_both" => true, "line" => 130));
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



require_once('battleClasses.php');
$boardController = new BoardContoller();
$strikeMoveController = new StrikeMoveController();

if ($both_set) {
  if ($new_move == false)
  {
    if ($curr_move == $login)
    {
      // status;
      // move, your, positions, timer:3;
      if (isset($_POST['get_status'])){
        set_new_move();
        $new_move = strtotime(get_ls_nm($id)["new_move"]);
        $timer = $new_move + $move_time - time();
        send_response(array("stage" => "move", "my_move" => true, "timer" => $timer));
      }
    }
    else
    {
      if (time() > ($both_set_time + $accept_time))
      {
        set_winner();
        //status;
        send_response(array("winner" => $login));
      }
      else
      {
        // status;
        // 2, 0, poses, 2a
        $timer = $both_set_time + $accept_time - time();
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
        // status ;
        // 3, 1, poses, 3
        if (isset($_POST['get_status'])){
          set_new_move();
          $new_move = strtotime(get_ls_nm($id)["new_move"]);
          $timer = $new_move + $move_time - time();//get_new_move
          send_response(array("stage" => "move", "my_move" => true, "timer" => $timer));
        }
      }
      else
      {
        if (time() > ($last_strike + $accept_time))
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
    }

    if ($last_strike == false || ($new_move > $last_strike))
    {
      if ($curr_move == $login)
      {
        //strike();
        //if strike finish game?
        // status;
        // 2, 0, poses, 2b
        if (isset($_POST['strike'])){
          if (strike())
          {
            set_winner();
            //status;
            send_response(array("winner" => $login));
          }
          else
          {
            $last_strike = strtotime(get_ls_nm($id)["last_strike"]);
            $timer = $last_strike + $accept_time - time();
            send_response(array("stage" => "accept new move", "my_move" => false, "timer" => $timer));
          }
        }
        else
        {
          $timer = $new_move + $move_time - time();
          send_response(array("stage" => "move", "my_move" => true, "timer" => $timer));
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
        else
        {
          $timer = $new_move + $move_time - time();
          send_response(array("stage" => "move", "my_move" => false, "timer" => $timer));
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
    if (set_position($_POST["set_position"]))
    {
      $timer = $battle_start + $set_time - time();
      $my_board = get_login_board($login, $id);
      if ($enemy_isset)
        set_both_set_time();
      send_response(array("stage" => "set position", "my_move" => false, "timer" => $timer));
    }
    else
    {
      $timer = $battle_start + $set_time - time();
      $my_board = get_login_board($login, $id);
      send_response(array("stage" => "set position", "my_move" => false, "timer" => $timer));
    }
  }
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
// b
