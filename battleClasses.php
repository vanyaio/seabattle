<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('consts.php');
class BoardContoller{
    public function __construct()//*
    {
        global $db;
        global $login;
        global $id;

        $query = "SELECT board from battle_positions where login = :login and id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':login', $login);
        $statement->bindValue(':id', $id);
        $statement->execute();

        $board_str = $statement->fetch()['board'];


        $this->board = [];
        for ($i = 0; $i < BOARD_SIZE; $i++){
            $this->board[] = [];
            for ($j = 0;  $j < BOARD_SIZE; $j++){
                if ($board_str)
                  $this->board[$i][$j] = $board_str[$i * BOARD_SIZE + $j];
                else
                  $this->board[$i][$j] = 0;
            }
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
        $query = "update battle_positions set board='$board_str' where id=$id and login='$login'";
        $statement = $db->prepare($query);
        $statement->execute();
        $statement->closeCursor();
    }
    
    public function setBoardNull(){
        global $db;
        global $login;
        global $id;
        $query = "update battle_positions set board=NULL where id=$id and login='$login'";
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

        $this->ship_refs_board = [];
        for ($i = 0; $i < BOARD_SIZE; $i++)
        {
            $this->ship_refs_board[] = [];
            for ($j = 0; $j < BOARD_SIZE; $j++)
                $this->ship_refs_board[$i][] = null;
        }
        global $my_position_str;
        foreach ($my_position_str as $ship_position){
            $ship = new Ship($ship_position, $this);
            $this->ships[] = $ship;
        }
    }

    public function set_position()
    {
        $positions = filter_input(INPUT_POST, 'set_position', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!isset($positions) || $positions == false)
        {
            return false;
        }

        foreach ($positions as $position)
        {
            $x = (int)$position['x'];
            $y = (int)$position['y'];
            $len = (int)$position['len'];
            $direction = (int)$position['direction'];
            //need to filter 'em

            for ($cell = 0; $cell < $len; $cell++)
            {
                if ($this->boardController->board[$x][$y] != OPENED_WATER){
                    //make board null again
                    $this->boardController->setBoardNull();
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
        
        $lens_counter = [];
        for ($i = 1; $i <= LENS_AMOUNT; $i++)
            $lens_counter[$i] = 0;
        
        $lens_required = array(null, ONE_AMOUNT, TWO_AMOUNT, THREE_AMOUNT, FOUR_AMOUNT);
        
        foreach ($this->ships as $ship){
            $lens_counter[$ship->position['len']]++;
        }
        
        for ($i = 1; $i <= LENS_AMOUNT; $i++){
            if ($lens_required[$i] != $lens_counter[$i]){
                $this->boardController->setBoardNull();
                return false;
            }
        }
        
        global $db;
        global $login;
        global $id;
        $positions = json_encode($positions);
        $query = "update positions_str set position='$positions' where id=$id and login='$login'";
        $statement = $db->prepare($query);
        $statement->execute();
        $statement->closeCursor();

        return true;
    }
  
    public function strike()
    {
        $x = (int)$_POST['strike']['x'];
        $y = (int)$_POST['strike']['y'];
        //filter 'em

        $ship = $this->ship_refs_board[$x][$y];
        if (isset($ship)){
            $ship->take_strike($x, $y);//here change board
            
            foreach ($this->ships as $ship_checking)
            {
                if (!$ship_checking->sank()){
                    return false;
                }
            }
            return true;
        }
        else{
            $this->boardController->setBoardCell($x, $y, MISS);
        }
    }
  

    //public $board;
    public $boardController;
    public $ship_refs_board;
    public $ships;
    //private $login;
    public $set;
}

class Ship{
    public function __construct($position, $strike_move_controller) {
        $this->position = $position;
        $this->strike_move_controller = $strike_move_controller;
        $this->boardController = $strike_move_controller->boardController;
        
        
        $set = $this->strike_move_controller->set;
        $x = (int)$position['x'];
        $y = (int)$position['y'];
        $len = (int)$position['len'];
        $direction = (int)$position['direction'];

        for ($cell = 0; $cell < $len; $cell++)
        {
            $this->strike_move_controller->ship_refs_board[$x][$y] = $this;
            if ($direction == RIGHT){
                $x++;
            }
            else{
                $y++;
            }
        }
        
        if (!$set)
        {
            $x = (int)$position['x'];
            $y = (int)$position['y'];
            for ($cell = 0; $cell < $len; $cell++){
                $this->boardController->setBoardCell($x, $y, SHIP_PART);
                
                for ($i = -1; $i <= 1; $i++){
                    for ($j = -1; $j <= 1; $j++){
                        if ($i == 0 && $j == 0)
                            continue;
                        $xs = $x + $i;
                        $ys = $y + $j;
                        
                        if ($this->strike_move_controller->ship_refs_board[$xs][$ys] == null)
                            $this->boardController->setBoardCell($xs, $ys, CLOSED_WATER);
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
        
        $x = (int)$position['x'];
        $y = (int)$position['y'];
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
            foreach ($this->cells as $key => $cell)
            {
                if ($cell['x'] == $x && $cell['y'] == $y){
                    //$cell['status'] = HIT;
                    $this->cells[$key]['status'] = HIT;
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
    
    public $strike_move_controller;
    public $boardController;
    public $position;
    public $cells;
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