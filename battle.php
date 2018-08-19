<?php

require('database.php');
require('util.php');
require("battle_util.php");

if (!is_logged_in)
  bad_request();

if (winner != null)
{

}
if (left_by_both)
{

}

if (!$both_set($id)){
  if (!$board_set && time_passed(time(), $battle_start + $set_time)){
      if (time_passed(time(), $battle_start + $set_time + $req_time)){
        left_by_both = true;
      }
      else {
        in_progress;
      }
  }

  if ($board_set && time_passed(time(), $battle_start + $set_time + $req_time)){
    left_by_both = true;
  }
}
else{
  if ($new_move == null){
    if (time_passed(time(), $battle_start + $set_time + $req_time)){
      left_by_both = true;
    }
  }
  else{
    if (($last_strike == null) || ($last_strike != null && ($new_move > $last_strike)))
    {
      if ($your_move)
      {
        if (time_passed(time(), $new_step_time + $move_time + $req_time))
          left_by_both;
        else
          in_progress;
      }
      else
      {
        if (time_passed(time(), $new_step_time + $move_time + $req_time))
          left_by_both;
      }
    }

    if ($last_strike != null && $last_strike > $new_move)
    {
      if (time_passed(time(), $last_strike + $accept_time + $req_time))
        left_by_both;
      if ($your_move)
      {
        if (time_passed(time(), $last_strike + $accept_time))
          in_progress;
      }
    }
  }
}
?>

<!-- a
















b -->
