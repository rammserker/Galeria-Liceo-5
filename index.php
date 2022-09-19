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
<!doctype html>
<html lang="es-UY">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jornada de Integración Liceo N⁰ 5</title>

        <link rel="stylesheet" href="css/galeria.css">
        <link rel="icon" type="image/x-icon" href="img/icon.ico">
    </head>
    <body>
        <header id="top">
            <h1>Jornada de Integración Liceo N⁰ 5</h1>
        </header>
        <form action="" method="post" autocomplete="off" enctype="multipart/form-data" class="upload">
            <input type="file" name="uploadfoto" title="Elegí una foto para agregar" required>
            <input type="hidden" name="action" value="upload">
            <input type="submit" value="<?php if (hasImage()) echo 'Cambiar imagen'; else echo 'Subir imagen'; ?>">
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
            <div>
                <img src="img/logo_utec.png" alt="Logo de UTEC">
                <img src="img/logo_utu.png" alt="Logo de UTU">
                <img src="img/logo_udelar.png" alt="Logo de UdelaR">
                <img src="img/logo_tip.png" alt="Logo de TI">
            </div>
            Copyright TIP 2022
        </footer>
<?php
if (count($imagenes) > 1)
{
?>
            <a href="#top" class="totop"></a>
<?php
}
?>
    <script src="js/galeria.js" async></script>
    </body>
</html>