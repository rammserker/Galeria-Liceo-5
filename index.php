<?php
require_once('./api/utils/utils.php');

// Gestión de cookie
$usrid = getUserId();

// Gestión de método HTTP
$method = $_SERVER[ 'REQUEST_METHOD' ];

if ($method == 'POST')
{
    $action = htmlspecialchars($_GET["action"]);
    
    if (isset($_POST["action"]))
    {
        $action = htmlspecialchars($_POST["action"]);
    }

    switch ($action)
    {
        case 'upload':
            // Se está subiendo una imagen por vía común
            if (!uploadImage())
            {
                // Si hubo error, mostrar error
                // echo "¡Error al subir la imagen!";
                // header('Refresh: 5');
                http_response_code(500);
                exit();
            }
        
            http_response_code(303);
            header("Location: /");
            exit();
            break;

        case 'like':
            $imgid = htmlspecialchars($_GET["imgid"]);
        
            if (isset($_POST["imgid"]))
            {
                $imgid = htmlspecialchars($_POST["imgid"]);
            }
    
            $likes = toggleLike($imgid);
        
            if ($likes === false)
            {
                http_response_code(500);
                exit();
            }
            
            http_response_code(303);
            header("Location: /");
            exit();
            break;
    }
    
}

$imagenes  = getImages("");
$timestamp = getLastTimestamp($imagenes);

?>
<html lang="es-UY">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jornada de Integración Liceo N⁰ 5</title>

        <link rel="stylesheet" href="css/galeria.css">

        <script src="js/galeria.js" async></script>
    </head>
    <body>
        <header id="top">
            <h1>Jornada de Integración Liceo N⁰ 5</h1>
        </header>
        <form action="" method="post" autocomplete="off" enctype="multipart/form-data" class="upload">
            <input type="file" name="uploadfoto" title="Elegí una foto para agregar" required>
            <input type="hidden" name="action" value="upload">
            <input type="submit" value="Subir imagen">
        </form>
        <main>
<?php
foreach ($imagenes as $img)
{
    imageTemplate($img);
}
?>
        </main>
        <footer>
<?php
if (count($imagenes) > 1)
{
?>
            <a href="#top" class="totop"></a>
<?php
}
?>
            Copyright TIP 2022
        </footer>
    </body>
</html>