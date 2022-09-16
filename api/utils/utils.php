<?php
///// Utilidades generales
function guidv4 ($data = null)
{
    // Función copiada de https://www.uuidgenerator.net/dev-corner/php
    $data = $data ?? random_bytes(16);
    
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function getUserId ()
{
    $usrid = null;
    
    if (!headers_sent() && !isset($_COOKIE["usr_token"]))
    {
        $usrid = guidv4();
        setcookie("usr_token", $usrid, time() + (60 * 60 * 24 * 365));
    }
    else
    {
        $usrid = $_COOKIE["usr_token"];
    }

    return $usrid;
}

function getLastTimestamp ($imgs)
{
    $last = count($imgs) > 0 ?
        max(array_map(function ($img) {
            return $img[ 'timestamp' ];
        }, $imgs)) :
        0;

    return $last;
}

///// Métodos de archivos
function getUtilsDir ()
{
    return dirname(__FILE__) . '/';
}

///// Métodos de imágenes
function getImages ($from = "")
{
    // Paths
    $path    = getUtilsDir() . "../../img/galeria";
    $outpath = "img/galeria";

    $usrid = getUserId();

    $imgs = glob("${path}/*.{jpg,jpeg,png,bmp,webp}", GLOB_BRACE);

    $respuesta = [];

    foreach ($imgs as $img)
    {
        $file = basename($img);
        list($imgid, $ext) = explode('.', $file);
        
        // Paths
        $likepath = "${path}/${imgid}.likes";
        $filepath = "${outpath}/${file}";
        
        // Leer likes
        $ref = fopen($likepath, 'r');
        $likes = fgets($ref);
        fclose($ref);

        $likes = json_decode($likes);

        $respuesta[] = [
            "id" => $imgid,
            "url" => $filepath,
            "likes" => count($likes),
            "liked" => array_search($usrid, $likes, true) > -1
        ];
    }

    return $respuesta;
}

function uploadImage ()
{
    // Paths
    $path    = getUtilsDir() . "../../img/galeria";
    $outpath = "img/galeria";
    
    $upload_file = basename($_FILES["uploadfoto"]["name"]);

    $uploadOk = 1;

    // Extension
    $file_ext = strtolower(pathinfo($upload_file, PATHINFO_EXTENSION));

    // Lugar final
    $usrid = getUserId();
    $target_file = "${path}/${usrid}.${file_ext}";

    // Verificar si es una imagen
    if (isset($_POST["submit"]))
    {
        $check = getimagesize($_FILES["uploadfoto"]["tmp_name"]);
        
        if ($check === false)
        {
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 1)
    {
        if (!move_uploaded_file($_FILES["uploadfoto"]["tmp_name"], $target_file))
        {
            $uploadOk = 0;
        }
        else
        {
            // Crear archivo de likes
            $file_like = "${path}/${usrid}.likes";
            $ref = fopen($file_like, 'w');
            fwrite($ref, json_encode([]));
            fclose($ref);
        }
    }

    return $uploadOk == 1;
}

///// Métodos de likes
function getLikes ($imgid)
{
    $path = getUtilsDir() . "../../img/galeria";
    $likepath = "${path}/${imgid}.likes";    

    // Leer likes
    $ref = fopen($likepath, 'r');

    if (!$ref)
    {
        return false;
    }
    
    $likes = fgets($ref);
    fclose($ref);

    $likes = json_decode($likes);

    return count($likes);
}

function toggleLike ($imgid)
{
    // Paths
    $usrid = getUserId();
    $path = getUtilsDir() . "../../img/galeria";
    $likepath = "${path}/${imgid}.likes";

    // Leer likes
    $ref = fopen($likepath, 'r');

    if (!$ref)
    {
        return false;
    }
    
    $likes = fgets($ref);
    fclose($ref);

    $likes = json_decode($likes);

    if (array_search($usrid, $likes, true) === false)
    {
        // No existía el like
        $likes[] = $usrid;
    }
    else
    {
        // Hay like
        // Se filtra de esta forma y no con array_filter
        // para evitar que al serializar como json lo tome
        // como un objeto
        $aux = [];
        foreach ($likes as $like)
        {
            if ($usrid != $like)
                $aux[] = $like;
        }

        $likes = $aux;
    }

    $ref = fopen($likepath, 'w');
    fwrite($ref, json_encode($likes));
    fclose($ref);

    return count($likes);
}

function likeImage ($imgid)
{
    // Paths
    $usrid = getUserId();
    $path = getUtilsDir() . "../../img/galeria";
    $likepath = "${path}/${imgid}.likes";

    // Leer likes
    $ref = fopen($likepath, 'r');
    $likes = fgets($ref);
    fclose($ref);

    $likes = json_decode($likes);

    if (array_search($usrid, $likes, true) === false)
    {
        $likes[] = $usrid;

        $ref = fopen($likepath, 'w');
        fwrite($ref, json_encode($likes));
        fclose($ref);

        return true;
    }

    return false;
}

function unlikeImage ($imgid)
{
    // Paths
    $usrid = getUserId();
    $path = getUtilsDir() . "../../img/galeria";
    $likepath = "${path}/${imgid}.likes";

    // Leer likes
    $ref = fopen($likepath, 'r');
    $likes = fgets($ref);
    fclose($ref);

    $likes = json_decode($likes);

    if (array_search($usrid, $likes, true) > -1)
    {
        $aux = array_filter($likes, function ($k, $val) {
            return $usrid != $val;
        }, ARRAY_FILTER_USE_BOTH);

        $ref = fopen($likepath, 'w');
        fwrite($ref, json_encode($aux));
        fclose($ref);

        return true;
    }

    return false;
}

///// Métodos HTML
function imageTemplate ($img)
{
    $usrid = getUserId();
    
    if (!isset($img))
    {
        $img = [
            "id" => "%%placeholder%%",
            "url" => "%%placeholder%%",
            "likes" => 0,
            "liked" => false
        ];
    }
?>
<figure
    <?php if ($img['liked']) echo 'class="liked"'; ?>
    <?php if ($img['id'] == $usrid) echo 'class="propia"'?>
>
    <img src="<?=$img['url']?>" alt="Foto del liceo 5">
    <figcaption>
        <form action="" method="post" class="like">
            <input type="hidden" name="action" value="like">
            <input type="hidden" name="imgid" value="<?=$img['id']?>">
            <input
                type="submit"
                 <?php if ($img['id'] == $usrid) echo "disabled"?>
                value="<?=$img['liked'] ? "No me gusta" : "Me gusta"?>">
        </form>
        <p><?=$img['likes']?> Me gusta</p>
    </figcaption>
</figure>
<?php
}