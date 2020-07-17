<?php
require_once 'keys.php';
require_once 'database.php';
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
$db;

function connect() {
    global $enable_debug;
    try {
        $db = &$GLOBALS['db'];
        if ($db) return $db;
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

function createTableSnippets() {
    $db = connect();
    global $enable_debug;
    $createSQL = 'CREATE TABLE IF NOT EXISTS `snippets` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `name` VARCHAR(25) NOT NULL UNIQUE,
        `width` INT(5) NOT NULL,
        `height` INT(5) NOT NULL)';
    try {
        $db->query($createSQL);
    } catch (Exception $ex) {
        LOG_M('Create table users failed: ', $ex->getMessage());
    }
};

function insertSnippets() {
    $db = connect();
    createTableSnippets();
    $stmt = $db->prepare('INSERT INTO `snippets` (`name`, `width`, `height`) VALUES (?, ?, ?)');
    $values = [
        ['camera', 2048, 2048],
        ['hud', 4167, 4167],
        ['confetti', 1028, 856],
        ['sunglasses', 1000, 471],
        ['glasses', 2000, 700],
        ['moustasche', 900, 762],
        ['beard', 700, 587],
        ['santa_hat', 1552, 1456],
        ['mexican_hat', 669, 640]
    ];
    foreach ($values as $value) {
        try {
            $stmt->execute($value);
        } catch(Exception $ex){
            // LOG_M($ex->getMessage());
            return false;
        }
    }
};

function selectSnippet($s) {
    $db = connect();
    if (isset($s['id'])) {
        $stmt = $db->prepare('SELECT * FROM `snippets` WHERE `id`=?');
        $stmt->execute([$s['id']]);
    } else if (isset($s['name'])) {
        $stmt = $db->prepare('SELECT * FROM `snippets` WHERE `name`=?');
        $stmt->execute([$s['name']]);
    }
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res;
};


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

createTableUsers();
insertSnippets();
?>