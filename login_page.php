<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>WELCOME!</title>
  </head>
  <body>
    <?php
    if ($incorrect_login_input){
      echo "incorrect!!";
    }
    ?>
    <form action="." method="post">
        <input type="text" name="login">
        <input type="text" name="password">

        <input type="submit" value="send">
    </form>
  </body>
</html>
