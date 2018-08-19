<?php
    if (!last_battle_is_over($_SESSION['login'])){
      echo "kek";
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




  </body>
</html>
