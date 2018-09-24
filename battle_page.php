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

    <button onlcick="xy1()">3,3</button>
    <button onlcick="xy2()">5,5</button>
    <button onlcick="xy3()">6,5</button>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js" charset="utf-8"></script>
    <script type="text/javascript">
        function set(){
          $.post(
            "battle.php",
            {
              "battle_page_loaded": true,
              "set_position": [
                  {'x': 5, 'y': 5, 'len': 1, 'direction': 1},
                  {'x': 0, 'y': 0, 'len': 2, 'direction': 1}
              ]
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
              "strike": {
                  'x' : x, 'y' : y
              }
            },
            function(data, status){
              alert(data);
            }
          )
        }
        
        
        var x = 1;
        var y = 1;
        function xy1(){
            x=3;
            y=3;
        }
        function xy2(){
            x = 5;
            y = 5;
        }
        function xy3(){
            x = 6;
            y = 5;
        }        
    </script>
  </body>
</html>
