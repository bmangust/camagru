<?php
require_once 'setup.php';
require_once '../log.php';

function authorize($user, $pwd) {
    $userRes = selectUser($user);
    print_r($userRes);
    if (isset($userRes['name'])) {
        return hash('whirlpool', $pwd) === $userRes['password'];
    }
    return false;
}

function sendEmail($userEmail, $message)
{
    $url = 'https://api.elasticemail.com/v2/email/send';

    try{
            $post = array('from' => 'info@akraig.tk',
            'fromName' => 'Akraig camagru',
            'apikey' => '69C6805321231CDEADB9E90D6D7012CE249BF2F1E0A85AE76D957ABD4A88FF571E89054F5E8F0F3A4E72CB6B9D836C6D',
            'subject' => $message['title'],
            'to' => $userEmail,
            'bodyHtml' => '<h1>'.$message['title'].'</h1>',
            'bodyText' => $message['body'],
            'isTransactional' => false);
            
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            
            $result=curl_exec ($ch);
            curl_close ($ch);
            
            echo $result;	
    }
    catch(Exception $ex){
        echo $ex->getMessage();
    }
}

// register new user
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Register') {
    $user = selectUser($_POST['username']);
    if (!isset($user['name'])) {
        $pwd = hash('whirlpool', $_POST['password']);
        insertUser($_POST['username'], $_POST['email'], $pwd);
        session_start();
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['is_auth'] = true;
        print_r($_SERVER['is_auth']);
        $msg=hash('crc32', 'User succesfully created');
        header("Location: ../index.php?route=menu&msg={$msg}&class=msg");
    } else {
        unset($_SESSION['user']);
        $_SESSION['is_auth'] = false;
        $msg=hash('crc32', 'Username already exists');
        header("Location: ../index.php?route=create&msg={$msg}&class=error");
    }
}

// login
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Login') {
    if (authorize($_POST['username'], $_POST['password'])) {
        session_start();
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['is_auth'] = true;
        print_r($_SESSION['is_auth']);
        $msg=hash('crc32', 'Sucsessfully authorized');
        header("Location: ../index.php?route=menu&msg={$msg}&class=msg");
    } else {
        unset($_SESSION['user']);
        $_SESSION['is_auth'] = false;
        $msg=hash('crc32', 'Authorized persons only');
        header("Location: ../index.php?route=login&msg={$msg}&class=error");
    }
}

// restore password
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Restore password') {
    $user = getUserEmail($_POST['username']);
    print_r($user);
    if ($user) {
        $bytes = random_bytes(5);
        $code = bin2hex($bytes);
        $message['title'] = 'Restore email';
        $message['body'] = 'Restore email link: http://localhost/camagru/config/users.php?restore='.$code;
        print_r($message);
        setUserCode($code, $user['name'], $user['email']);
        //$userEmail, $subject, $message
        sendEmail($user['email'], "restore email", $message);
    }    
}


if ($_GET && isset($_GET['restore'])) {
    session_start();
    $_SESSION['user'] = false;
    $_SESSION['is_auth'] = false;
    LOG_M($_SESSION);
    header("Location: ../index.php?route=menu");
}

if ($_GET && isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_start();
    $_SESSION['user'] = false;
    $_SESSION['is_auth'] = false;
    LOG_M($_SESSION);
    header("Location: ../index.php?route=menu");
}