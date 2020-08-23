<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes', 'User.class.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
session_start();
$username = $_SESSION['user'];

// LOG_M("files", $_FILES);
// LOG_M("post", $_POST);

function addSnippet($snippetData, $target_file)
{
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
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
        $name = strlen($_FILES["file"]["name"]) > 50 ? 'capture' : $_FILES["file"]["name"];
        $target_name = "{$username}_".time()."_".basename($name);
        $target_file = "{$target_dir}".$target_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if($_POST) {
            $check = mime_content_type($_FILES["file"]["tmp_name"]);
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
            return;
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $_SESSION['msg'][] = "The file ". basename( $_FILES["file"]["name"]). " has been uploaded";
            } else {
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'Sorry, there was an error uploading your file';
                return;
            }
        }
    } else if (isset($_POST['capture'])) {
        $target_dir = "../assets/uploads/";
        $target_name = "{$username}_".time()."_capture.jpg";
        $target_file = "{$target_dir}".$target_name;
        $capture = json_decode($_POST['capture']);
        $bg = imagecreatefrompng($capture->src);
        $width = imagesx( $bg );
        $height = imagesy( $bg );
        $dest = imagecreatetruecolor($width, $height);

        imagecopy($dest, $bg, 0, 0, 0, 0, $width, $height);
        imagejpeg($dest, $target_file);
        imagedestroy($dest);
        imagedestroy($bg);

    } else if (isset($_POST['snippet'])) {
        // upload default image only if some snippets were added
        $target_dir = "../assets/uploads/";
        $target_name = "{$username}_".time()."_default.jpg";
        $target_file = "{$target_dir}".$target_name;
        $file = "../assets/bg.jpg";
        $bg = imagecreatefromjpeg($file);
        $width = imagesx( $bg );
        $height = imagesy( $bg );
        $dest = imagecreatetruecolor($width, $height);

        imagecopy($dest, $bg, 0, 0, 0, 0, $width, $height);
        imagejpeg($dest, $target_file);
        imagedestroy($dest);
        imagedestroy($bg);

    } else {
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'No file was created - empty canvas';
        header("Location: ../index.php?route=create");
    }
    if (isset($_POST['snippet'])) {
        foreach($_POST['snippet'] as $snippet) {
            $s = json_decode($snippet);
            addSnippet($s, $target_file);
        }
    }
    // add database record about new file. Delete file on error
    if (DBOinsertUpload($target_name, $_SESSION['user'])) {
        $_SESSION['msg'][] = 'Your file was successfuly uploaded';
    } else {
        unlink($target_file);
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'Database error';
    }
    header("Location: ../index.php?route=create");
}

function updateLike($data)
{
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    if ($input['liked'] === true) {
        if (DBOinsertLike($_SESSION['user'], $input['id'])) {
            $data['success'] = true;
            $data['data'] = 'Like added';
        }
    } else {
        if (DBOremoveLike($_SESSION['user'], $input['id'])) {
            $data['success'] = true;
            $data['data'] = 'Like removed';
        }
    }
    return $data;
}

function updatePrivacy($data)
{
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    if ($input['private'] === true) {
        if (DBOupdatePrivacy($input['id'], true)) {
            $data['success'] = true;
            $data['data'] = 'private';
        }
    } else {
        if (DBOupdatePrivacy($input['id'], false)) {
            $data['success'] = true;
            $data['data'] = 'public';
        }
    }
    return $data;
}

function removePicture($data)
{
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    if ($input['remove'] === true) {
        if (DBOremovePicture($input['id'])) {
            $data['success'] = true;
            $data['data'] = 'removed';
        }
    }
    return $data;
}

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', $_SERVER['REQUEST_URI']);
$path = explode('?', @$url[4])[0] ?? null;
$id = @$url[5] ?? null;
$data = ['success' => false];

switch ($method) {
    case 'GET':
        if ($path === 'more') {
            $limit = $_GET['limit'] ?? 2;
            $offset = $_GET['offset'] ?? 0;
            $params = ['offset'=>$offset, 'limit'=>$limit];
            $data['success'] = true;
            $data['message'] = "limit: $limit, offset: $offset, url: {$_SERVER['REQUEST_URI']}";
            $data['data'] = DBOselectAllUploads($_SESSION['user'], $params);
        } else if ($path === 'size') {
            $data['success'] = true;
            $data['data'] = DBOgetGallerySize($_SESSION['user']);
        } else if ($path === 'my') {
            $offset = $_GET['offset'] ?? 0;
            $limit = DBOgetGallerySize($_SESSION['user']);
            $params = ['offset'=>$offset, 'limit'=>$limit, 'filter' => ['table'=>'us', 'value'=>$_SESSION['user']]];
            $data['success'] = true;
            $data['data'] = DBOselectUploads($params);
        } else if ($path === 'user') {
            $user = $_GET['user'] ?? false;
            if (!$user) {
                $data['success'] = false;
                $data['data'] = "User not found";
                break;
            }
            $offset = $_GET['offset'] ?? 0;
            $limit = DBOgetGallerySize($user);
            $params = ['offset'=>$offset, 'limit'=>$limit, 'filter' => ['value'=>$user]];
            $data['success'] = true;
            $data['data'] = DBOselectAllUploads($user, $params);
        } else if ($path === 'getLikes') {
            $imgid = $_GET['id'];
            $data['success'] = true;
            if (!$imgid) {
                $data['data'] = 0;
            }
            $data['data'] = DBOgetNumberOfLikes($imgid);
        } else if ($path === 'getComments') {
            $imgid = $_GET['id'];
            if (!$imgid) {
                $data['data'] = '';
            }
            $data['data'] = DBOselectComments($imgid);
            if ($data['data']) $data['success'] = true;
        }
        break;
    
    case 'POST':
        if (isset($_FILES['file'])) {
            uploadFile();
        } else if ($path === 'comment') {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, TRUE);
            if (DBOaddComment($input['message'], $input['author'], $input['imgid'])) {
                $user = DBOselectImgAuthor($input['imgid']);
                if ($user) {
                    $data['success'] = true;
                    $data['data'] = $input['message'];
                    $message['title'] = 'New comment on your photo!';
                    $message['body'] = "Hi, {$user['name']}. Your photo just got a new comment. View it here: http://localhost/camagru/index.php?route=image&id={$input['imgid']}";
                    User::sendEmail($user['email'], $message);
                } else {
                    $data['data'] = $user;
                }
            }
        }
        break;
    
    case 'PUT':
        if ($path === 'like') {
            $data = updateLike($data);
        } else if ($path === 'private') {
            $data = updatePrivacy($data);
        } else if ($path === 'public') {
            $data = updatePrivacy($data);
        }
        break;
    
    case 'DELETE':
        if ($path === 'remove') {
            $data = removePicture($data);
        }
        break;
    default: 
        $data['message'] = 'Method is not supported';
}
header('Content-Type: application/json; charset=UTF-8');
echo json_encode($data);