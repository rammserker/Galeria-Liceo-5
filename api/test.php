<?php
require_once("./utils/utils.php");

$method = $_SERVER[ 'REQUEST_METHOD' ];
$usr_id = getUserId();

switch ($method)
{
    case 'POST':
        $action = htmlspecialchars($_GET["action"]);
    
        if (isset($_POST["action"]))
        {
            $action = htmlspecialchars($_POST["action"]);
        }
        echo $action;
        
        break;
}