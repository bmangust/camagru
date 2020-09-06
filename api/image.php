<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes', 'User.class.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes', 'Logger.class.php'));
session_start();
Logger::Dlog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr' => 'session', 'message' => $_SESSION]);

function addSnippet($snippetData, $target_file)
{
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    Logger::Dlog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr' => 'imageFileType', 'message' => $imageFileType]);
    Logger::Dlog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr' => 'snippet data', 'message' => $snippetData]);
    if (file_exists($target_file)) {
    Logger::Dlog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr' => 'target_file', 'message' => $target_file]);
    }
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
    Logger::Dlog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr' => 'dest', 'message' => $dest]);
    if (!$dest) {
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
    // $opacity = $snippetData->opacity ?? 0;
    $transparency = 1 - floatval($snippetData->opacity);
    Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => "transparency: " . $transparency]);
    if ($transparency > 0) {
        imagefilter($src, IMG_FILTER_COLORIZE, 0, 0, 0, 127 * $transparency);
    }
    imagecopyresampled($dest,$src, $leftOffset, $topOffset, $left, $top, $pngWidth, $pngHeight, $width, $height );

    imagejpeg($dest, $target_file);
    
    imagedestroy($dest);
    imagedestroy($src);

}

function uploadFile() {
    $target_dir = join(DIRECTORY_SEPARATOR, [__DIR__, '..', "assets", "uploads"]);
    if (!is_dir($target_dir)) {
        Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => "target dir: " . $target_dir . " does not exists" .PHP_EOL]);
        if (!mkdir($target_dir)) {
            Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => "cannot create dir: " . $target_dir . PHP_EOL]);
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = "Cannot create uploads directory";
            header("Location: ../index.php?route=create");
            return false;
            // die();
        }
    }
    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = join(DIRECTORY_SEPARATOR, [__DIR__, '..', "assets", "uploads"]);
        $name = strlen($_FILES["file"]["name"]) > 50 ? 'capture' : str_replace(" ", "_", $_FILES["file"]["name"]);
        $target_name = "{$_SESSION['user']}_".time()."_".basename($name);
        $target_file = "{$target_dir}".DIRECTORY_SEPARATOR.$target_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if($uploadOk && $_POST) {
            $check = mime_content_type($_FILES["file"]["tmp_name"]);
            if(strstr($check, "image") !== false) {
                Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => "File is an image - " . $check .PHP_EOL]);
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
        Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $_POST['capture']]);
        $target_name = "{$_SESSION['user']}_".time()."_capture.jpg";
        $target_file = $target_dir.DIRECTORY_SEPARATOR.$target_name;
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
        Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => 'No image was sent, but some snippets were. Take them']);
        // upload default image only if some snippets were added
        $target_name = "{$_SESSION['user']}_".time()."_default.jpg";
        $target_file = $target_dir.DIRECTORY_SEPARATOR.$target_name;
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
            Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => "snippet: " . print_r($s, true) . ", target_file: " . $target_file . PHP_EOL]);
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
    $input = json_decode($inputJSON, true);
    Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $input]);
    if ($_SESSION['user'] === false || $_SESSION['is_auth'] === false) {
        $data['data'] = 'Please log in to like and add comments';
    } else if ($input['liked'] === true) {
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
    $input = json_decode($inputJSON, true);
    Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $input]);
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
    $input = json_decode($inputJSON, true);
    Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $input]);
    if ($input['remove'] === true) {
        if (DBOremovePicture($input['id'])) {
            $data['success'] = true;
            $data['data'] = 'removed';
        }
    }
    return $data;
}

function addComment($data)
{
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $input]);
    if ($_SESSION['user'] === false || $_SESSION['is_auth'] === false) {
        $data['data'] = 'Please log in to like and add comments';
    } else if (DBOaddComment($input['message'], $input['author'], $input['imgid'])) {
        $user = DBOselectImgAuthor($input['imgid']);
        if (!$user) return $data;

        $data['success'] = true;
        $data['data'] = $input['message'];
        Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $data['data']]);

        if (!$user['notificationsEnable']) return $data;
        $message['title'] = 'New comment on your photo!';
        $message['body'] = "Hi, {$user['name']}. Your photo just got a new comment. View it here: http://localhost/camagru/index.php?route=image&id={$input['imgid']}";
        User::sendEmail($user['email'], $message);

    }
    return $data;
}

function changeAvatar($data)
{
    $inputJSON = file_get_contents('php://input');
    $avatar = json_decode($inputJSON, true);
    Logger::Ilog (['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'message' => $avatar]);
    $user = DBOselectUser($_SESSION['user']);
    $target_dir = join(DIRECTORY_SEPARATOR, ["..", "assets", "avatars", ""]);
    $target_name = "{$user['id']}.png";
    $target_file = $target_dir.$target_name;
    
    $bg = imagecreatefrompng($avatar['src']);
    if (!$bg) {
        $data['message'] = 'Avatar was not saved';
        return $data;
    }
    $width = imagesx( $bg );
    $height = imagesy( $bg );
    $size = min($height, $width);
    $dest = imagecreatetruecolor(200, 200);

    imagealphablending($dest, false);
    imagesavealpha($dest,true);
    if (!imagecopyresampled($dest, $bg, 0, 0, 0, 0, 200, 200, $size, $size) || 
        !imagepng($dest, $target_file) || 
        !DBOupdateAvatar($_SESSION['user'])
    ) $data['message'] = 'Avatar was not saved';
    else {
        $data['success'] = true;
        $data['message'] = 'Avatar saved';
    }
    imagedestroy($dest);
    imagedestroy($bg);
    return $data;
}

$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', $_SERVER['REQUEST_URI']);
$path = explode('?', @$url[4])[0] ?? null;
$id = @$url[5] ?? null;
$data = ['success' => false];

switch ($method) {
    case 'GET':
        Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr' => 'GET', 'message' => $_GET]);
        if ($path === 'more') {
            $limit = $_GET['limit'] ?? 2;
            $offset = $_GET['offset'] ?? 0;
            $params = ['offset'=>$offset, 'limit'=>$limit];
            $data['success'] = true;
            $data['message'] = "limit: $limit, offset: $offset, url: {$_SERVER['REQUEST_URI']}";
            $data['data'] = DBOselectAllUploads($_SESSION['user'], $params);
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'More uploads', 'message' => $data['data']]);
        } else if ($path === 'size') {
            $data['success'] = true;
            $data['data'] = DBOgetGallerySize($_SESSION['user']);
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'GallerySize', 'message' => $data['data']]);
        } else if ($path === 'my') {
            $offset = $_GET['offset'] ?? 0;
            $limit = DBOgetGallerySize($_SESSION['user']);
            $params = ['offset'=>$offset, 'limit'=>$limit, 'filter' => ['table'=>'us', 'value'=>$_SESSION['user']]];
            $data['success'] = true;
            $data['data'] = DBOselectUploads($params);
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'My uploads', 'message' => $data['data']]);
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
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> "profile gallery", 'message' => $data['data']]);
        } else if ($path === 'getLikes') {
            $imgid = $_GET['id'];
            $data['success'] = true;
            if (!$imgid) {
                $data['data'] = 0;
            }
            $data['data'] = DBOgetNumberOfLikes($imgid);
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'Number of likes', 'message' => $data['data']]);
        } else if ($path === 'getComments') {
            $imgid = $_GET['id'];
            if (!$imgid) {
                $data['data'] = '';
            }
            $data['data'] = DBOselectComments($imgid);
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'Comments', 'message' => $data['data']]);
            if ($data['data']) $data['success'] = true;
        }
        break;
    
    case 'POST':
        Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'POST', 'message' => $_POST]);
        if (isset($_FILES['file'])) {
            Logger::Ilog(['function' => __FILE__.':'.__FUNCTION__, 'line' => __LINE__, 'descr'=> 'FILES', 'message' => $_FILES]);
            uploadFile();
        } else if ($path === 'comment') {
            $data = addComment($data);
        } else if ($path === 'avatar') {
            $data = changeAvatar($data);
        }
        break;
    
    case 'PUT':
        if ($path === 'like') {
            $data = updateLike($data);
        } else if ($path === 'private' || $path === 'public') {
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