<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BoardContoller{
    public function __construct($login, $id)//*
    {
      global $db;

      $query = "SELECT board from battle_positions where login = :login and id = :id";
      $statement = $db->prepare($query);
      $statement->bindValue(':login', $login);
      $statement->bindValue(':id', $id);
      $statement->execute();

      $board_str = $statement->fetch()['board'];
      
      if ($board_str){
          $this->board = [];
          for ($i = 0; $i < BOARD_SIZE; $i++){
              $this->board[] = [];
              for ($j = 0;  $j < BOARD_SIZE; $j++){
                  $this->board[$i][$j] = $board_str[$i * BOARD_SIZE + $j];
              }
          }
      }
      else{
          $board = null;
      }
    }

    public function setBoardCell($x, $y, $value){
        $this->board[$x][$y] = $value;
        
        $board_str = '';
        for ($i = 0; $i < BOARD_SIZE; $i++){
            for ($j = 0; $j < BOARD_SIZE; $j++){
                $board_str .= $this->board[$i][$j];
            }
        }
        
        global $db;
        global $login;
        global $id;
        $query = "update battle_positions set board=$board_str where id=$id and login=$login";
        $statement = $db->prepare($query);
        $statement->execute();
        $statement->closeCursor();
    }
    
    public function setBoardNull(){
        global $db;
        global $login;
        global $id;
        $query = "update battle_positions set board=NULL where id=$id and login=$login";
        $statement = $db->prepare($query);
        $statement->execute();
        $statement->closeCursor();  
    }

    public $board;
}
class StrikeMoveController
{
    public function __construct() {//?login as param
        global $boardController;
        $this->boardController = $boardController;

        global $login_isset;
        $this->set = $login_isset;


        for ($i = 0; $i < BOARD_SIZE; $i)
        {
            $this->ship_refs_board[] = [];
            for ($j = 0; $j < BOARD_SIZE; $j++)
                $this->ship_refs_board[i][] = null;
        }
        global $my_position_str;
        foreach ($my_position_str as $ship_position){
            $ship = new Ship($ship_position, $this);
            $this->ships[] = $ship;
        }
    }

    public function set_position()//CHECK FOR SHIPS QUANTITY
    {
        $positions = filter_input(INPUT_POST, 'set_position', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!isset($positions) || $positions == false)
        {
            return false;
        }

        foreach ($positions as $position)
        {
            $x = $position['x'];
            $y = $position['y'];
            $len = $position['len'];
            $direction = $position['direction'];
            //need to filter 'em

            for ($cell = 0; $cell < $len; $cell++)
            {
                if ($this->boardController->board[x][y] != OPENED_WATER){
                    //make board null again

                    return false;
                }
                $this->boardController->setBoardCell($x, $y, SHIP_PART);
                if ($direction == RIGHT){
                    $x++;
                }
                else{
                    $y++;
                }
            }

            $this->ships[] = new Ship($position, $this);//change board & ship_refs
        }

        global $db;
        global $login;
        global $id;
        $positions = json_encode($positions);
        $query = "update positions_str set position=$positions where id=$id and login=$login";
        $statement = $db->prepare($query);
        $statement->execute();
        $statement->closeCursor();

        return true;
    }
  
    public function strike()
    {
        $x = $_POST['strike']['x'];
        $y = $_POST['strike']['y'];
        //filter 'em

        $ship = $this->ship_refs_board[$x][$y];
        if (isset($ship)){
            $ship->take_strike($x, $y);//here change board
            foreach ($this->ships as $ship)
            {
                if (!$ship->sank()){
                    return false;
                }
            }
        }
        else{
            $this->boardController->setBoardCell($x, $y, MISS);
        }
    }
  

    //public $board;
    public $boardController;
    public $ship_refs_board;
    private $ships;
    //private $login;
    public $set;
}

class Ship{
    public function __construct($position, $strike_move_controller) {
        $this->position = $position;
        $this->strike_move_controller = $strike_move_controller;
        $this->boardController = $strike_move_controller->boardController;
        
        
        $set = $this->strike_move_controller->set;
        $x = $position['x'];
        $y = $position['y'];
        $len = $position['len'];
        $direction = $position['direction'];

        for ($cell = 0; $cell < $len; $cell++)
        {
            $this->strike_move_controller->ship_refs_board[x][y] = $this;
            if ($direction == RIGHT){
                $x++;
            }
            else{
                $y++;
            }
        }
        
        if (!$set)
        {
            $x = $position['x'];
            $y = $position['y'];
            for ($cell = 0; $cell < $len; $cell++){
                $this->boardController->setBoardCell($x, $y, SHIP_PART);
                
                for ($i = -1; $i <= 1; $i++){
                    for ($j = -1; $j <= 1; $j++){
                        if ($i == 0 && $j == 0)
                            continue;
                        $xs = $x + $i;
                        $ys = $y + $j;
                        
                        if ($this->strike_move_controller->ship_refs_board[$xs][$ys] == null)
                            $this->boardController->setBoardCell($xs, $ys, OPENED_WATER);
                    }
                }
                
                if ($direction == RIGHT){
                  $x++;
                }
                else{
                  $y++;
                }
                
            }
        }
        
        $x = $position['x'];
        $y = $position['y'];
        $this->cells = [];
        for ($cell = 0; $cell < $len; $cell++)
        {
            $this->cells[] = ['x' => $x, 'y' => $y, 'status' => $this->boardController->board[$x][$y]];
            if ($direction == RIGHT){
                $x++;
            }
            else{
                $y++;
            }
        }       
    }
    
    public function take_strike($x, $y){
        if ($this->boardController->board[$x][$y] == SHIP_PART){
            $this->boardController->setBoardCell($x, $y, HIT); 
            foreach ($this->cells as $cell)
            {
                if ($cell['x'] == $x && $cell['y'] == $y){
                    $cell['status'] = HIT;
                    break;  
                }
            }
            
            if ($this->sank()){          
                $x = $this->position['x'];
                $y = $this->position['y'];
                $len = $this->position['len'];
                $direction = $this->position['direction'];
                for ($cell = 0; $cell < $len; $cell++){
                    $this->boardController->setBoardCell($x, $y, DEAD_SHIP);

                    for ($i = -1; $i <= 1; $i++){
                        for ($j = -1; $j <= 1; $j++){
                            if ($i == 0 && $j == 0)
                                continue;
                            $xs = $x + $i;
                            $ys = $y + $j;

                            if ($this->boardController->board[$xs][$ys] != DEAD_SHIP)
                                $this->boardController->setBoardCell($xs, $ys, DEAD_WATER);
                        }
                    }

                    if ($direction == RIGHT){
                      $x++;
                    }
                    else{
                      $y++;
                    }

                }                
            }
        }
    }
    
    public function sank(){
        foreach ($this->cells as $cell)
        {  
          if ($cell['status'] == SHIP_PART){
            return false;
          }
        }
        return true;
    }
    
    private $strike_move_controller;
    private $boardController;
    private $position;
    private $cells;
}









//c 
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
//b