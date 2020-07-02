<?php
require_once 'setup.php';

function authorize($user, $pwd) {
    $userRes = selectUser($user);
    if (isset($userRes['name'])) {
        return hash('whirlpool', $pwd) === $userRes['password'];
    }
    return false;
}

// print_r($_POST);

if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Register') {
    $user = selectUser($_POST['username']);
    if (isset($user['name'])) {
        $pwd = hash('whirlpool', $_POST['password']);
        insertUser($_POST['username'], $_POST['email'], $pwd);
        $msg=hash('crc32', 'User succesfully created');
        header("Location: ../index.php?route=login&msg={$msg}&class=msg");
    } else {
        $msg=hash('crc32', 'Username already exists');
        header("Location: ../index.php?route=create&msg={$msg}&class=error");
    }
}

if ($_POST && isset($_POST['submit']) && $_POST['submit'] === 'Login') {
    if (authorize($_POST['username'], $_POST['password'])) {
        $msg=hash('crc32', 'Sucsessfully authorized');
        header("Location: ../index.php?msg={$msg}&class=msg");
        // echo '<br>ok<br>';
    } else {
        $msg=hash('crc32', 'Authorized persons only');
        header("Location: ../index.php?route=login&msg={$msg}&class=error");
        // echo '<br>not auth<br>';
    }
    // echo $msg;
}