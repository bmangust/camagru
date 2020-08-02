<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes','User.class.php'));
$GLOBALS['ACTION_RESTORE'] = "RESTORE_PASSWORD";
$GLOBALS['ACTION_ACTIVATE'] = "ACTIVATE_ACCOUNT";
session_start();

// log POST request to file
function logToFile($var) {
    file_put_contents( 'debug' . time() . '.log', var_export($var, true) );
}

if ($_POST && isset($_POST['submit'])) {
    $email = $_POST['email'] ? strtolower($_POST['email']) : "";
    if ($_POST['submit'] === 'Register') {
        User::registerUser($_POST['username'], $email, $_POST['password']);
    } else if ($_POST['submit'] === 'Login') {
        User::userLogin($_POST['username'], $_POST['password']);
    } else if ($_POST['submit'] === 'Restore password') {
        User::restorePassword($email);
    } else if ($_POST['submit'] === 'Save') {
        User::savePassword($email, $_POST['password']);
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
    } else if ($_GET['action'] == 'settings') {
        header("Location: ../index.php?route=settings");
    } else if ($user && $_GET['email'] === $user['email'] && $_GET['action'] === $GLOBALS['ACTION_RESTORE'] && $_GET['code'] === $user['restoreCode']) {
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
