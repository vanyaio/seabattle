<?php
session_start();

require_once("database.php");
require_once("battle_util.php");
//auth

//check for already in battle

$login = $_SESSION['login'];
if ($_POST['start_search']){
    $login_in_queue = $db_obj->select("login", "search", "login = :login",
        ":login", $login);
    if ($login_in_queue != false){
        echo 'already in queue';
        die();
    }

    $search_start = date($time_format, time());
    $db_obj->insert("search", "(login, entry_time)", "(:login, :entry_time)",
        ":login", $login, ":entry_time", $search_start);

    echo 'added';
} elseif ($_POST['get_search_status']){
    $enemy_login = $db_obj->hardQuery("SELECT login FROM search INNER JOIN
                      (SELECT MIN(entry_time) AS min_time from search WHERE login != :login)min_time_db
                      on search.entry_time = min_time;", ":login", $login)['login'];
    if ($enemy_login != false){
        $db_obj->delete("search", "login = :login",
            ":login", $login);
        $db_obj->delete("search", "login = :login",
            ":login", $enemy_login);
        start_battle($login, $enemy_login);

        echo 'started battle';
    }
    else{
        $time_now = date($time_format, time());
        $db_obj->update("search", "last_req_time = :time_now", "login =  :login",
            ":time_now", $time_now, ":login", $login);

        echo 'updated req_time';
    }
} elseif ($_POST['stop_search']){
    $db_obj->delete("search", "login = :login",
        ":login", $login);

    echo 'removed from queue';
}
