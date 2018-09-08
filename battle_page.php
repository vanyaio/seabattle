<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <button onclick="set()" type="button" id="set">set</button>
    <button onclick="status()" type="button" id="status">status</button>
    <button onclick="strike()" type="button" id="strike">strike</button>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js" charset="utf-8"></script>
    <script type="text/javascript">
        function set(){
          $.post(
            "battle.php",
            {
              "battle_page_loaded": true,
              "set_position": true
            },
            function(data, status){
              alert(data);
            }
          )
        }
        function status(){
          $.post(
            "battle.php",
            {
              "battle_page_loaded": true,
              "get_status": true
            },
            function(data, status){
              alert(data);
            }
          )
        }
        function strike(){
          $.post(
            "battle.php",
            {
              "battle_page_loaded": true,
              "strike": "strike_str"
            },
            function(data, status){
              alert(data);
            }
          )
        }
    </script>
  </body>
</html>
