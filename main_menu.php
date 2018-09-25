<?php

    require_once("battle_util.php");
    if (!last_battle_is_over($_SESSION['login'])){
      header("Location: battle.php");
      die();
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <p>main page</p>

    <br>

    <form  action="." method="post">
      <input type="hidden" name="logout" value="true">
      <input type="submit" value="log out">
    </form>

    <br>
    
    <div id="search" onclick="search()">
        SEARCH
    </div>
    <div id="search_status" onclick="get_search_status()">
        STATUS
    </div>
    <div id="stop_search" onclick="stop_search()">
        STOP 
    </div>
    
<!--    <form  action="battle.php" method="post">
      <input type="hidden" name="start_battle" value="true">
      <input type="submit" value="BATTLE">
    </form>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js" charset="utf-8"></script>
    <script>
        
    </script>
</body>
</html>
