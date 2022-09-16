<?php
require_once("./utils/utils.php");

$method = $_SERVER[ 'REQUEST_METHOD' ];
$usr_id = getUserId();

switch ($method)
{
    case 'GET':
        $imgid = htmlspecialchars($_GET["imgid"]);
        $likes = getLikes($imgid);

        if ($likes === false)
        {
            http_response_code(404);
            exit();
        }

        http_response_code(200);
        echo $likes;
        break;
    
    case 'POST':
        $imgid = htmlspecialchars($_GET["imgid"]);
    
        if (isset($_POST["imgid"]))
        {
            $imgid = htmlspecialchars($_POST["imgid"]);
        }

        $likes = toggleLike($imgid);
    
        if ($likes === false)
        {
            http_response_code(404);
            exit();
        }
        
        http_response_code(200);
        echo $likes;
        break;

    case 'DELETE':
        $imgid = htmlspecialchars($_GET["imgid"]);
    
        if (isset($_DELETE["imgid"]))
        {
            $imgid = htmlspecialchars($_DELETE["imgid"]);
        }
    
        if (unlikeImage($imgid))
        {
            http_response_code(200);
        }
        else
        {
            http_response_code(404);
        }
        break;
}