<?php
require_once 'setup.php';
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));

LOG_M($_POST);

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
    global $enable_debug;
    $url = 'https://api.elasticemail.com/v2/email/send';

    try{
            $post = array('from' => 'ftf.pollux@gmail.com',
            'fromName' => 'Akraig camagru',
            'apikey' => $GLOBALS['ELASTIC_EMAIL_API_KEY'],
            'subject' => $message['title'],
            'to' => $userEmail,
            'bodyHtml' => '<table><tr><td>'.$message['body'].'</td></tr></table>',
            // 'bodyText' => 'Text Body',
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
        LOG_M($ex->getMessage());
    }
}

// register new user
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Register') {
    $user = selectUser($_POST['username']);
    if (!isset($user['email'])) {
        $pwd = hash('whirlpool', $_POST['password']);
        insertUser($_POST['username'], strtolower($_POST['email']), $pwd);
        session_start();
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['is_auth'] = true;
        $msg=hash('crc32', 'User succesfully created');
        header("Location: ../index.php?route=menu&msg={$msg}&class=msg");
    } else {
        unset($_SESSION['user']);
        $_SESSION['is_auth'] = false;
        $msg=hash('crc32', 'This email as already been taken');
        header("Location: ../index.php?route=create&msg={$msg}&class=error");
    }
}

// login
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Login') {
    if (authorize($_POST['username'], $_POST['password'])) {
        session_start();
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['is_auth'] = true;
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
    $email = strtolower($_POST['email']);
    $user = selectUser($email);
    LOG_M('user', $user);
    if ($user) {
        $bytes = random_bytes(5);
        $code = bin2hex($bytes);
        $message = [];
        $message['title'] = "Restore password";
        $message['body'] = "Restore email link: http://localhost/camagru/config/users.php?restore={$code}&email={$email}";
        setUserCode($code, $user['name'], $email);
        sendEmail($email, $message);
        $msg=hash('crc32', 'Check your email');
        header("Location: ../index.php?route=login&msg={$msg}&class=msg");
    } else {
        $msg=hash('crc32', 'Email not found');
        header("Location: ../index.php?route=login&msg={$msg}&class=error");
    }
}

// file_put_contents( 'debug' . time() . '.log', var_export( $_POST, true));

// save restored password
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Save') {
    $email = strtolower($_POST['email']);
    $user = selectUser($email);
    LOG_M('user', $user);
    if ($user) {
        $res = updatePassword($user['email'], hash('whirlpool', $_POST['password']));
        $msg=hash('crc32', 'Your new password saved');
        header("Location: ../index.php?route=login&msg={$msg}&class=msg");
        echo json_encode($res);
        
    } else {
        $msg=hash('crc32', 'Email not found');
        echo json_encode($res);
        // header("Location: ../index.php?route=login&msg={$msg}&class=error");
    }
}

if ($_GET && isset($_GET['restore'])) {
    $user = selectUser($_GET['email']);
    if ($_GET['email'] === $user['email'] && $_GET['restore'] === $user['restoreCode']) {
        header("Location: ../index.php?route=restore&email={$user['email']}");
    } else {
        $msg=hash('crc32', 'Wrong restore code');
        header("Location: ../index.php?route=create&msg={$msg}&class=error");
    }
}

if ($_GET && isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_start();
    $_SESSION['user'] = false;
    $_SESSION['is_auth'] = false;
    LOG_M($_SESSION);
    header("Location: ../index.php?route=menu");
}