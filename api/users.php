<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
$GLOBALS['ACTION_RESTORE'] = "RESTORE_PASSWORD";
$GLOBALS['ACTION_ACTIVATE'] = "ACTIVATE_ACCOUNT";
if (@!$_SESSION) {
    session_start();
}

// LOG_M($_POST);

// check if user entered valid password
function authorize($user, $pwd) {
    $userRes = selectUser($user);
    if (isset($userRes['name'])) {
        return hash('whirlpool', $pwd) === $userRes['password'];
    }
    return false;
}

// check if user confirmed email
function checkUserVerification($username) {
    $user = selectUser($username);
    if ($user && $user['verified'] == true) {
        return true;
    }
    return false;
}

function sendEmail($userEmail, $message) {
    global $enable_debug;
    $url = 'https://api.elasticemail.com/v2/email/send';

    try{
            $post = array('from' => 'ftf.pollux@gmail.com',
            'fromName' => 'Akraig camagru',
            'apikey' => $GLOBALS['ELASTIC_EMAIL_API_KEY'],
            'subject' => $message['title'],
            'to' => $userEmail,
            'bodyHtml' => '<h3>'.$message['body'].'</h3>',
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
            
            $res = json_decode(curl_exec ($ch), true);
            curl_close ($ch);
            LOG_M($res);
            return $res['success'];
    }
    catch(Exception $ex){
        LOG_M($ex->getMessage());
        return false;
    }
}

// log POST request to file
function logToFile($var) {
    file_put_contents( 'debug' . time() . '.log', var_export($var, true) );
}

// register new user
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Register') {
    $user = selectUser($_POST['email']);
    $_SESSION['is_auth'] = false;
    if (!isset($user['email'])) {
        $email = strtolower($_POST['email']);
        $pwd = hash('whirlpool', $_POST['password']);
        if(!insertUser($_POST['username'], $email, $pwd)) {
            unset($_SESSION['user']);
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'This username has already been taken';
            header("Location: ../index.php?route=register");
        }
        $bytes = random_bytes(5);
        $code = bin2hex($bytes);
        $message = [];
        $message['title'] = "Activate account";
        $message['body'] = "Activate account link: http://localhost/camagru/api/users.php?action={$GLOBALS['ACTION_ACTIVATE']}&code={$code}&email={$email}";
        setUserCode($code, $_POST['username'], $email);
        if (!sendEmail($email, $message)) {
            unset($_SESSION['user']);
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Server error, please try again';
            header("Location: ../index.php?route=register");
        }
        // $_SESSION['user'] = $_POST['username'];
        $_SESSION['msg'][] = 'Check your email';
        header("Location: ../index.php?route=login");
    } else {
        unset($_SESSION['user']);
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'This email has already been taken';
        header("Location: ../index.php?route=register");
    }
}

// login
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Login') {
    if (authorize($_POST['username'], $_POST['password'])) {
        if (!checkUserVerification($_POST['username'])) {
            unset($_SESSION['user']);
            $_SESSION['is_auth'] = false;
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Email is not confirmed';
            header("Location: ../index.php?route=login");
        }
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['is_auth'] = true;
        header("Location: ../index.php?route=menu");
    } else {
        unset($_SESSION['user']);
        $_SESSION['is_auth'] = false;
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'Username or password in wrong';
        header("Location: ../index.php?route=login");
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
        $message['body'] = "Restore password link: http://localhost/camagru/api/users.php?action={$GLOBALS['ACTION_RESTORE']}&code={$code}&email={$email}";
        setUserCode($code, $user['name'], $email);
        if (!sendEmail($email, $message)) {
            unset($_SESSION['user']);
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Server error, please try again';
            header("Location: ../index.php?route=forgot");
        }
        $_SESSION['msg'][] = 'Check your email';
        header("Location: ../index.php?route=login");
    } else {
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'Email not found';
        header("Location: ../index.php?route=forgot");
    }
}

// save restored password
if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Save') {
    $email = strtolower($_POST['email']);
    $user = selectUser($email);
    LOG_M('user', $user);
    if ($user) {
        updatePassword($user['email'], hash('whirlpool', $_POST['password']));
        $_SESSION['msg'][] = 'Your new password saved';
        header("Location: ../index.php?route=login");
    } else {
        $_SESSION['class'] = 'error';
        $_SESSION['msg'][] = 'Email not found';
        header("Location: ../index.php?route=restore");
    }
}

if ($_GET && isset($_GET['action'])) {
    LOG_M("get", $_GET);
    $user = null;
    if (isset($_GET['email'])) {
        $user = selectUser($_GET['email']);
    }
    if ($_GET['action'] == 'logout') {
        $_SESSION['user'] = false;
        $_SESSION['is_auth'] = false;
        header("Location: ../index.php?route=menu");
    }
    else if ($user && $_GET['email'] === $user['email'] && $_GET['action'] === $GLOBALS['ACTION_RESTORE'] && $_GET['code'] === $user['restoreCode']) {
        header("Location: ../index.php?route=restore&email={$user['email']}");
    } else if ($user && $_GET['email'] === $user['email'] && $_GET['action'] === $GLOBALS['ACTION_ACTIVATE'] && $_GET['code'] === $user['restoreCode']) {
        activateUserAccount($user['name']);
        $_SESSION['msg'][] = 'Email confirmed';
        header("Location: ../index.php?route=menu");
    } else {
        $_SESSION['class'] = 'error';
        if ($_GET['action'] === $GLOBALS['ACTION_RESTORE']) {
            $_SESSION['msg'][] = 'Wrong restore code';
        } else if ($_GET['action'] === $GLOBALS['ACTION_ACTIVATE']) {
            $_SESSION['msg'][] = 'Wrong restore code';
        } else if ($_GET['action'] !== 'logout') {
            $_SESSION['msg'][] = 'Please follow the link in your email';
        }
        header("Location: ../index.php?route=menu");
    }
}
