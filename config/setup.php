<?php
require_once 'keys.php';
require_once 'database.php';
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));
$db;

function DBOconnect() {
    try {
        $db = &$GLOBALS['db'];
        if ($db) return $db;
        $db = new PDO($GLOBALS['dsn'], $GLOBALS['dbuser'], $GLOBALS['dbpwd'], array(PDO::ATTR_PERSISTENT => true));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->query('CREATE DATABASE IF NOT EXISTS akraig_camagru');
        $db->exec('USE akraig_camagru');
    } catch (PDOException $ex) {
        LOG_M('Connection failed: ', $ex->getMessage());
        die();
    }
    return $db;
}

function DBOcreateTableUsers() {
    $db = DBOconnect();
    $createSQL = 'CREATE TABLE IF NOT EXISTS `users` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `name` VARCHAR(25) NOT NULL UNIQUE, 
        `email` VARCHAR(40) NOT NULL UNIQUE, 
        `password` VARCHAR(128) NOT NULL,
        `verified` BOOLEAN DEFAULT FALSE,
        `restoreCode` VARCHAR(10),
        `is_admin` BOOLEAN DEFAULT FALSE)';
    try {
        $db->query($createSQL);
    } catch (Exception $ex) {
        LOG_M('Create table users failed: ', $ex->getMessage());
        die();
    }
};

function DBOcreateTableSnippets() {
    $db = DBOconnect();
    $createSQL = 'CREATE TABLE IF NOT EXISTS `snippets` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `name` VARCHAR(25) NOT NULL UNIQUE,
        `width` INT(5) NOT NULL,
        `height` INT(5) NOT NULL)';
    try {
        $db->query($createSQL);
    } catch (Exception $ex) {
        LOG_M('Create table snippets failed: ', $ex->getMessage());
    }
};

function DBOinsertSnippets() {
    $db = DBOconnect();
    DBOcreateTableSnippets();
    $stmt = $db->prepare('INSERT INTO `snippets` (`name`, `width`, `height`) VALUES (?, ?, ?)');
    $values = [
        ['camera', 2048, 2048],
        ['hud', 4167, 4167],
        ['confetti', 1028, 856],
        ['sunglasses', 1000, 471],
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

function DBOselectSnippet($s) {
    $db = DBOconnect();
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


function DBOinsertUser($username, $email, $pwd) {
    $db = DBOconnect();
    $stmt = $db->prepare('INSERT INTO `users` (`name`, `email`, `password`) VALUES (:username, :email, :pwd)');
    return $stmt->execute([':username'=>$username, ':email'=>$email, ':pwd'=>$pwd]);
};

function DBOselectUsers() {
    $db = DBOconnect();
    $stmt = $db->prepare('SELECT `name` FROM `users`');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
};

function DBOselectUser($user) {
    $db = DBOconnect();
    $stmt = $db->prepare('SELECT * FROM `users` WHERE `name`=? OR `email`=?');
    $stmt->execute([$user, $user]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res;
};

function DBOsetUserCode($code, $user) {
    $db = DBOconnect();
    $stmt = $db->prepare('UPDATE `users` SET restoreCode=? WHERE `name`=? OR `email`=?');
    return $stmt->execute([$code, $user, $user]);
};

function DBOupdatePassword($email, $passwd) {
    $db = DBOconnect();
    $stmt = $db->prepare("UPDATE `users` SET `password`=?, `restoreCode`='' WHERE `email`=?");
    return $stmt->execute([$passwd, $email]);
};

function DBOupdateEmail($email, $newEmail) {
    $db = DBOconnect();
    $user = DBOselectUser($newEmail);
    if (isset($user['email'])) {
        return false;
    }
    try {
        $stmt = $db->prepare("UPDATE `users` SET `email`=? WHERE `email`=?");
        return $stmt->execute([$newEmail, $email]);
    } catch(Exception $ex){
        LOG_M($ex->getMessage());
        return false;
    }
};

function DBOupdateUsername($email, $newUsername) {
    $db = DBOconnect();
    $user = DBOselectUser($newUsername);
    if (isset($user['email'])) {
        return false;
    }
    try {
        $stmt = $db->prepare("UPDATE `users` SET `name`=? WHERE `email`=?");
        return $stmt->execute([$newUsername, $email]);
    } catch(Exception $ex){
        LOG_M($ex->getMessage());
        return false;
    }
};

function DBOactivateUserAccount($username) {
    $db = DBOconnect();
    $stmt = $db->prepare("UPDATE `users` SET `verified`=?, `restoreCode`='' WHERE `name`=?");
    return $stmt->execute([true, $username]);
}

function DBOgetUserEmail($user) {
    $db = DBOconnect();
    $stmt = $db->prepare('SELECT `email` FROM `users` WHERE `name`=?');
    $stmt->execute([$user]);
    $email = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    if (count($email) === 1) {
        return $email[0];
    }
};

function DBOdisconnect() {
    $db = &$GLOBALS['db'];
    $db = null;
};

DBOcreateTableUsers();
DBOinsertSnippets();
?>