<?php
require_once 'setup.php';
require_once '../log.php';

function authorize($user, $pwd) {
    $userRes = selectUser($user);
    if (isset($userRes['name'])) {
        return hash('whirlpool', $pwd) === $userRes['password'];
    }
    return false;
}

if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Register') {
    $user = selectUser($_POST['username']);
    if (isset($user['name'])) {
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

if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Login') {
    if (authorize($_POST['username'], $_POST['password'])) {
        session_start();
        $_SESSION['user'] = $_POST['username'];
        $_SESSION['is_auth'] = true;
        print_r($_SERVER['is_auth']);
        $msg=hash('crc32', 'Sucsessfully authorized');
        header("Location: ../index.php?route=menu&msg={$msg}&class=msg");
    } else {
        unset($_SESSION['user']);
        $_SESSION['is_auth'] = false;
        $msg=hash('crc32', 'Authorized persons only');
        header("Location: ../index.php?route=login&msg={$msg}&class=error");
    }
}

if ($_GET && isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_start();
    $_SESSION['user'] = false;
    $_SESSION['is_auth'] = false;
    LOG_M($_SESSION);
    header("Location: ../index.php?route=menu");
}