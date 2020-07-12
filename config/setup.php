<?php
require_once 'keys.php';
require_once 'database.php';
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
$db;

function connect() {
    global $enable_debug;
    try {
        $db = &$GLOBALS['db'];
        $db = new PDO($GLOBALS['dsn'], $GLOBALS['dbuser'], $GLOBALS['dbpwd'], array(PDO::ATTR_PERSISTENT => true));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query('CREATE DATABASE IF NOT EXISTS akraig_camagru');
        $db->exec('USE akraig_camagru');
    } catch (PDOException $ex) {
        LOG_M('Connection failed: ', $ex->getMessage());
    }
    return $db;
}

function createTableUsers() {
    $db = connect();
    global $enable_debug;
    $createSQL = 'CREATE TABLE IF NOT EXISTS `users` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `name` VARCHAR(25) NOT NULL UNIQUE, 
        `email` VARCHAR(40) NOT NULL UNIQUE, 
        `password` VARCHAR(128) NOT NULL,
        `verified` BOOLEAN DEFAULT FALSE,
        `restoreCode` VARCHAR(10))';
    try {
        $db->query($createSQL);
    } catch (Exception $ex) {
        LOG_M('Create table users failed: ', $ex->getMessage());
    }
};

function createTableMessages() {
    $db = connect();
    global $enable_debug;
    $createSQL = 'CREATE TABLE IF NOT EXISTS `messages` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `message` VARCHAR(128) NOT NULL UNIQUE, 
        `code` VARCHAR(8) NOT NULL)';
    $db->query($createSQL);
    $stmt = $db->prepare('INSERT INTO `messages` (`message`, `code`) VALUES (?, ?)');
    $messages = ['This email has already been taken', 'This username has already been taken', 'User succesfully created', 'Username or password in wrong', 'Authorized persons only', 'Sucsessfully authorized', 'Wrong restore code', 'Your new password saved', 'Check your email', 'Email not found', 'Email confirmed', 'Email is not confirmed', 'Server error, please try again'];
    foreach($messages as $message) {
        try {
            $stmt->execute([$message, hash('crc32', $message)]);
        } catch (Exception $ex) {
            return;
        }
    }
};

function getMessage($code) {
    $db = connect();
    try {
        $stmt = $db->prepare('SELECT `message` FROM `messages` WHERE `code`=?');
        $stmt->execute([$code]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($res['message'])) {
            return $res['message'];
        }
    } catch  (PDOException $e) {
        return 'Unknown error';
    }
}

function insertUser($username, $email, $pwd) {
    $db = connect();
    $stmt = $db->prepare('INSERT INTO `users` (`name`, `email`, `password`) VALUES (:username, :email, :pwd)');
    return $stmt->execute([':username'=>$username, ':email'=>$email, ':pwd'=>$pwd]);
};

function selectUsers() {
    $db = connect();
    $stmt = $db->prepare('SELECT `name` FROM `users`');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
};

function selectUser($user) {
    $db = connect();
    $stmt = $db->prepare('SELECT * FROM `users` WHERE `name`=? OR `email`=?');
    $stmt->execute([$user, $user]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res;
};

function setUserCode($code, $user) {
    $db = connect();
    $stmt = $db->prepare('UPDATE `users` SET restoreCode=? WHERE `name`=? OR `email`=?');
    return $stmt->execute([$code, $user, $user]);
};

function updatePassword($email, $passwd) {
    $db = connect();
    $stmt = $db->prepare("UPDATE `users` SET `password`=?, `restoreCode`='' WHERE `email`=?");
    return $stmt->execute([$passwd, $email]);
};

function activateUserAccount($username) {
    $db = connect();
    $stmt = $db->prepare("UPDATE `users` SET `verified`=?, `restoreCode`='' WHERE `name`=?");
    return $stmt->execute([true, $username]);
}

function getUserEmail($user) {
    $db = connect();
    $stmt = $db->prepare('SELECT `email` FROM `users` WHERE `name`=?');
    $stmt->execute([$user]);
    $email = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (count($email) === 1) {
        return $email[0];
    }
};

function disconnect() {
    $db = &$GLOBALS['db'];
    $db = null;
};


createTableMessages();
createTableUsers();

?>