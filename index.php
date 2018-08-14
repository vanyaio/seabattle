<?php
session_start();

require('database.php');

$incorrect_login_input = false;

if ($_SESSION['login'] == null || $_SESSION['password'] == null) {
  $password = filter_input(INPUT_POST, 'password');
  $login = filter_input(INPUT_POST, 'login');

  if ($password != null && $login != null){
    if (check_login_password($login, $password)){
      $_SESSION['login'] = $login;
      $_SESSION['password'] = $password;
      require("main_menu.php");
    }
    else {
      $incorrect_login_input = true;
      require("login_page.php");
    }
  }
  else{
    require("login_page.php");
  }
}
else {
  if (check_login_password($_SESSION['login'], $_SESSION['password'])){
    require('main_menu.php');
  }
  else {
    require("login_page.php");
  }
}
?>
