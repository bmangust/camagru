<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));


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
        $target_file = $target_dir . basename($_FILES["file"]["name"][$i]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["file"]["tmp_name"][$i]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".".PHP_EOL;
                $uploadOk = 1;
            } else {
                echo "File is not an image.".PHP_EOL;
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.".PHP_EOL;
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["file"]["size"][$i] > 1000000) {
            echo "Sorry, your file is too large.".PHP_EOL;
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.".PHP_EOL;
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $target_file)) {
                echo "The file ". basename( $_FILES["file"]["name"][$i]). " has been uploaded.".PHP_EOL;
            } else {
                echo "Sorry, there was an error uploading your file.".PHP_EOL;
            }
        }
    }
}