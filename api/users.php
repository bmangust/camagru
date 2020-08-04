<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes', 'User.class.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, 'globals.php'));
session_start();

// log POST request to file
function logToFile($var) {
    file_put_contents( 'debug' . time() . '.log', var_export($var, true) );
}

if ($_POST && (isset($_POST['submit']) || isset($_POST['action']))) {
    LOG_M("post",$_POST);
    $email = $_POST['email'] ? strtolower($_POST['email']) : "";
    if ($_POST['submit'] === 'Register') {
        User::registerUser($_POST['username'], $email, $_POST['password']);
    } else if ($_POST['submit'] === 'Login') {
        User::userLogin($_POST['username'], $_POST['password']);
    } else if ($_POST['submit'] === 'Restore password') {
        User::restorePassword($email);
    } else if ($_POST['submit'] === 'Save') {
        User::savePassword($email, $_POST['password']);
    } else if ($_POST['submit'] === 'Update email') {
        User::updateEmail($email, $_POST['newEmail']);
    } else if ($_POST['submit'] === 'Update username') {
        User::updateUsername($email, $_POST['username']);
    } else if ($_POST['action'] === 'Delete account') {
        User::deleteAccount($_SESSION['user']);
    }
}

if ($_GET && isset($_GET['action'])) {
    LOG_M("get", $_GET);
    $user = null;
    if (isset($_GET['email'])) {
        $user = DBOselectUser($_GET['email']);
    } else if (isset($_SESSION['user'])) {
        $user = DBOselectUser($_SESSION['user']);
    }
    if ($_GET['action'] == 'logout') {
        $_SESSION['user'] = false;
        $_SESSION['is_auth'] = false;
        header("Location: ../index.php?route=menu");
    } else if ($_GET['action'] == 'profile') {
        header("Location: ../index.php?route=profile");
    } else if ($_GET['action'] === $GLOBALS['ACTION_UPDATE_PASS']) {
        header("Location: ../index.php?route=restore");
    } else if ($_GET['action'] === $GLOBALS['ACTION_UPDATE_EMAIL']) {
        header("Location: ../index.php?route=update_email");
    } else if ($_GET['action'] === $GLOBALS['ACTION_UPDATE_USERNAME']) {
        header("Location: ../index.php?route=update_username");
    } else if ($user && $_GET['email'] === $user['email'] && $_GET['action'] === $GLOBALS['ACTION_RESTORE'] && $_GET['code'] === $user['restoreCode']) {
        header("Location: ../index.php?route=restore&email={$user['email']}");
    } else if ($user && $_GET['email'] === $user['email'] && $_GET['action'] === $GLOBALS['ACTION_ACTIVATE'] && $_GET['code'] === $user['restoreCode']) {
        DBOactivateUserAccount($user['name']);
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
