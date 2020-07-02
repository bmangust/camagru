<?php
require_once 'database.php';
$db = null;

function connect() {
    try {
        $db = &$GLOBALS['db'];
        $db = new PDO($GLOBALS['dsn'], $GLOBALS['user'], $GLOBALS['pwd'], array(PDO::ATTR_PERSISTENT => true));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query('CREATE DATABASE IF NOT EXISTS akraig_camagru');
        $db->exec('USE akraig_camagru');
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    return $db;
}

function createTableUsers() {
    $db = $GLOBALS['db'];
    if (!$db) {
        $db = connect();
    }
    $createSQL = 'CREATE TABLE IF NOT EXISTS `users` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `name` VARCHAR(25) NOT NULL UNIQUE, 
        `email` VARCHAR(40) NOT NULL, 
        `password` VARCHAR(128) NOT NULL)';
    $db->query($createSQL);
};

function createTableMessages() {
    $db = $GLOBALS['db'];
    if (!$db) {
        $db = connect();
    }
    $createSQL = 'CREATE TABLE IF NOT EXISTS `messages` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `message` VARCHAR(128) NOT NULL UNIQUE, 
        `code` VARCHAR(8) NOT NULL)';
    $db->query($createSQL);
    $stmt = $db->prepare('INSERT INTO `messages` (`message`, `code`) VALUES (?, ?)');
    $messages = ['Username already exists', 'User succesfully created', 'Authorized persons only', 'Sucsessfully authorized'];
    foreach($messages as $message) {
        $stmt->execute([$message, hash('crc32', $message)]);
    }
};

function getMessage($code) {
    $db = &$GLOBALS['db'];
    if (!$db) {
        $db = connect();
    }
    $stmt = $db->prepare('SELECT `message` FROM `messages` WHERE `code`=?');
    $stmt->execute([$code]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($res['message'])) {
        return $res['message'];
    }
    return 'Unknown error';
}

function insertUser($username, $email, $pwd) {
    $db = &$GLOBALS['db'];
    if (!$db) {
        $db = connect();
    }
    $stmt = $db->prepare('INSERT INTO `users` (`name`, `email`, `password`) VALUES (:username, :email, :pwd)');
    return $stmt->execute([':username'=>$username, ':email'=>$email, ':pwd'=>$pwd]);
};

function selectUsers() {
    $db = &$GLOBALS['db'];
    if (!$db) {
        $db = connect();
    }
    $stmt = $db->prepare('SELECT `name` FROM `users`');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
};

function selectUser($user) {
    $db = &$GLOBALS['db'];
    if (!$db) {
        $db = connect();
    }
    $stmt = $db->prepare('SELECT `name`, `password` FROM `users` where `name`=?');
    $stmt->execute([$user]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res;
};

function disconnect() {
    $db = &$GLOBALS['db'];
    $db = null;
};


// createTableMessages();

?>