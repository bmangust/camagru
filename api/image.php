<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
session_start();
$username = $_SESSION['user'];

LOG_M("files", $_FILES);
LOG_M("post", $_POST);

function addSnippet($snippetData, $target_file)
{
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    LOG_M ("imageFileType:", $imageFileType);
    switch($imageFileType) {
        case 'png':
            $dest = imagecreatefrompng($target_file);
            break;
        case 'jpg':
        case 'jpeg':
            $dest = imagecreatefromjpeg($target_file);
            break;
        case 'gif':
            $dest = imagecreatefromgif($target_file);
            break;
        default:
            $dest = null;
    }
    if ($dest == null) {
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'Error when create image';
        header("Location: ../index.php?route=create");
        echo 'Error when create image';
        die();
    }
    $src = imagecreatefrompng("../{$snippetData->path}");

    // absolute values
    $destwidth = imagesx( $dest );
    $destheight = imagesy( $dest );
    $srcwidth = imagesx( $src );
    $srcheight = imagesy( $src );
    
    if ($snippetData->top < 0) {
        $top = ( -$snippetData->offsetTop * $srcheight ) / ( $snippetData->height );
        $topOffset = 0;
        $hidden = ( -$snippetData->top * $snippetData->drawerHeight / 100 );
        $pngHeight = ( $snippetData->height - $hidden ) * $destheight / $snippetData->drawerHeight;
        $height = $srcheight - $top;
    } else {
        $top = 0;
        $topOffset = $snippetData->top * $destheight / 100;
        $pngHeight = $destheight * ( $snippetData->height / $snippetData->drawerHeight );
        $height = $srcheight;
    }
    if ($snippetData->left < 0) {
        $left = ( -$snippetData->offsetLeft *  $srcwidth ) / ( $snippetData->width );
        $leftOffset = 0;
        $hidden = ( -$snippetData->left * $snippetData->drawerWidth / 100 );
        $pngWidth = ( $snippetData->width - $hidden ) * $destwidth / $snippetData->drawerWidth;
        $width = $srcwidth - $left;
    } else {
        $left = 0;
        $leftOffset = $snippetData->left * $destwidth / 100;
        $pngWidth = $destwidth * ( $snippetData->width / $snippetData->drawerWidth );
        $width = $srcwidth;
    }

    imagealphablending($src, false);
    imagesavealpha($src,true);
    imagecopyresampled($dest,$src, $leftOffset, $topOffset, $left, $top, $pngWidth, $pngHeight, $width, $height );

    imagejpeg($dest, $target_file);
    
    imagedestroy($dest);
    imagedestroy($src);

}

function uploadFile() {
    global $username;
    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "../assets/uploads/";
        $target_file = "{$target_dir}{$username}_".time()."_".basename($_FILES["file"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if($_POST) {
            $check = mime_content_type($_FILES["file"]["tmp_name"]);
            LOG_M("check", $check);
            if(strstr($check, "image") !== false) {
                LOG_M ("File is an image - " . $check .PHP_EOL);
                $uploadOk = 1;
            } else {
                $_SESSION['msg'][] = "File is not an image";
                $uploadOk = 0;
            }
        }

        // Check file size
        if ($uploadOk && $_FILES["file"]["size"] > 1000000) {
            $_SESSION['msg'][] = 'Sorry, your file is too large';
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($uploadOk && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            $_SESSION['msg'][] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed';
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Your file was not uploaded';
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $_SESSION['msg'][] = "The file ". basename( $_FILES["file"]["name"]). " has been uploaded";
            } else {
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'Sorry, there was an error uploading your file';
                LOG_M ("Sorry, there was an error uploading your file ". basename( $_FILES["file"]["name"]));
            }
        }
    } else {
        LOG_M("no file uploaded, take default");
        $target_dir = "../assets/uploads/";
        $target_file = "{$target_dir}{$username}_".time()."_template.jpg";
        $file = "../assets/bg.jpg";
        $bg = imagecreatefromjpeg($file);
        $width = imagesx( $bg );
        $height = imagesy( $bg );
        echo "w:".$width.", h:".$height;
        $dest = imagecreatetruecolor($width, $height);

        imagecopy($dest, $bg, 0, 0, 0, 0, $width, $height);
        imagejpeg($dest, $target_file);
        imagedestroy($dest);
        imagedestroy($bg);

    }
    if (isset($_POST['snippet'])) {
        foreach($_POST['snippet'] as $snippet) {
            $s = json_decode($snippet);
            print_r($s);
            addSnippet($s, $target_file);
        }
    }
}

uploadFile();
header("Location: ../index.php?route=create");