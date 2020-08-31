<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes', 'User.class.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'classes', 'Logger.class.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, 'globals.php'));
session_start();
$method = $_SERVER['REQUEST_METHOD'];
$url = explode('/', $_SERVER['REQUEST_URI']);
$path = explode('?', @$url[4])[0] ?? null;

if ($_POST && (isset($_POST['submit']) || isset($_POST['action']))) {
    Logger::Dlog(['function' => __FILE__.__FUNCTION__, 'line' => __LINE__, 'descr' => 'POST', 'message' => $_POST]);
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
    Logger::Dlog(['function' => __FILE__.__FUNCTION__, 'line' => __LINE__, 'descr' => 'GET', 'message' => $_GET]);
    $user = null;
    if (isset($_GET['email'])) {
        $user = DBOselectUser($_GET['email']);
    } else if (isset($_SESSION['user'])) {
        $user = DBOselectUser($_SESSION['user']);
    }
    if ($_GET['action'] == 'logout') {
        $_SESSION['user'] = false;
        $_SESSION['is_auth'] = false;
        setcookie('user', '', time() - 3600);
        header("Location: ../index.php?route=menu");
    } else if ($_GET['action'] === $GLOBALS['ACTION_UPDATE_PASS']) {
        header("Location: ../index.php?route=restore");
    } else if ($_GET['action'] === $GLOBALS['ACTION_UPDATE_EMAIL']) {
        header("Location: ../index.php?route=update_email");
    } else if ($_GET['action'] === $GLOBALS['ACTION_UPDATE_USERNAME']) {
        header("Location: ../index.php?route=update_username");
    } else if ($_GET['action'] === 'selectNotifications') {
        $res = DBOselectUser($_SESSION['user']);
        if (!$res) $data = ['success' => false, 'message' => 'Database error'];
        $data = ['success' => true, 'value' => $res['notificationsEnable']];
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
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
} else if ($method == 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);
    Logger::Ilog(['function' => __FILE__.__FUNCTION__, 'line' => __LINE__, 'descr' => 'input', 'message' => $input]);
    if ($input['action'] == 'Change notifications') {
        $res = User::changeNotifications($_SESSION['user'], $input['value']);
    } else if ($input['action'] == 'Update info') {
        $res = User::updateInfo($_SESSION['user'], $input['data']);
        if ($res['success'] && $input['data']['newUsername'] !== '') {
            $_SESSION['user'] = $input['data']['newUsername'];
        }
    }
    Logger::Ilog(['function' => __FILE__.__FUNCTION__, 'line' => __LINE__, 'descr' => 'changeNotifications', 'message' => $res]);

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($res);
}
