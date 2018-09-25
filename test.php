<?php
  if (isset($_POST['set_position']))
  {
    $stringa = $_POST['set_position'];
    //echo $stringa[1]['len'];
    foreach ($stringa as $ship)
    {
        echo $ship['x'] . '<br>';
    }
    echo "kek <br>";
    echo json_encode($stringa) . "<br> kekkfd <br>";
    echo json_decode($stringa) . "<br> kekkfd <kekke>";
    $arr = ['x' => 10, 'y' => 20];
    $arr = json_encode($arr);
    echo $arr;
    die();
  }
?>

<!-- <?php
//$data = new DateTime('2018-08-17 05:00:30');
//
//echo $data->format('Y-m-d H:i:s')."<br>";
//echo strtotime($data->format('Y-m-d H:i:s')) . "<br>";
//echo date('Y-m-d H:i:s', strtotime($data->format('Y-m-d H:i:s')) + 120);
//
//echo "<br>" . time() . "<br>";
//
//include("database.php");
//
//

?> -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <button onclick="set()" type="button" id="set">set</button>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js" charset="utf-8"></script>
    <script type="text/javascript">
    xex = {
      "set_position" : [
        { "x": 10, "y": 20},
        { "x": 110, "y": 2230, "len" : 2}
      ]
    }
    function set(){
      $.post(
        "test.php",
        xex,
        function(data, status){
          alert(data);
        }
      )
    }
    </script>
  </body>
</html>


<!-- fffffffff































v -->
