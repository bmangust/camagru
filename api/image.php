<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
session_start();
$username = $_SESSION['user'];

// log POST request to file
function logToFile($var) {
    file_put_contents( 'debug' . time() . '.jpg', $var );
}

// $request = file_get_contents('php://input');
// logToFile($request);
LOG_M("files", $_FILES);

foreach ($_FILES["file"]["error"] as $i => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $target_file = "{$target_dir}{$username}_".time()."_".basename($_FILES["file"]["name"][$i]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if($_POST) {
            $check = mime_content_type($_FILES["file"]["tmp_name"][$i]);
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
        if ($uploadOk && $_FILES["file"]["size"][$i] > 1000000) {
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
            if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $target_file)) {
                $_SESSION['msg'][] = "The file ". basename( $_FILES["file"]["name"][$i]). " has been uploaded";
            } else {
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'Sorry, there was an error uploading your file';
                LOG_M ("Sorry, there was an error uploading your file ". basename( $_FILES["file"]["name"][$i]));
            }
        }
    }
}
header("Location: ../index.php?route=create");