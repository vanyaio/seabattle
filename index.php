<?php
session_start();

require_once('database.php');
require_once('util.php');

if (isset($_POST['logout']) && $_POST['logout'] === 'true'){
  $_SESSION['login'] = null;
  $_SESSION['password'] = null;
}

$incorrect_login_input = false;

if ($_SESSION['login'] == null || $_SESSION['password'] == null) {
  $password = filter_input(INPUT_POST, 'password');
  $login = filter_input(INPUT_POST, 'login');

  if ($password != null && $login != null){
    if (check_login_password($login, $password)){
      $_SESSION['login'] = $login;
      $_SESSION['password'] = $password;
      require_once("main_menu.php");
    }
    else {
      $incorrect_login_input = true;
      require_once("login_page.php");
    }
  }
  else{
    require_once("login_page.php");
  }
}
else {
  if (check_login_password($_SESSION['login'], $_SESSION['password'])){
    require_once('main_menu.php');
  }
  else {
    require_once("login_page.php");
  }
}
