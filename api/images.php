<?php
require_once("./utils/utils.php");

$method = $_SERVER[ 'REQUEST_METHOD' ];
$usr_id = getUserId();

switch ($method)
{
    case 'GET':
        header('Content-type: application/json');
        $imagenes = getImages();

        // Obtener último timestamp
        $last = getLastTimestamp($imagenes);

        header("usrid: $usr_id");

        echo json_encode($imagenes);
        break;
    
    case 'POST':
        if (uploadImage())
        {
            http_response_code(201);
            header("Location: img/galeria/$file_name");
        }
        else
        {
            http_response_code(404);
        }
        break;
}