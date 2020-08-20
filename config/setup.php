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
        `isAdmin` BOOLEAN DEFAULT FALSE)';
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
        die();
    }
};

function DBOcreateTableUploads() {
    $db = DBOconnect();
    $createSQL = 'CREATE TABLE IF NOT EXISTS `uploads` 
        (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `userid` INT(5) UNSIGNED,
        `name` VARCHAR(50) NOT NULL,
        `rating` INT(1) UNSIGNED DEFAULT 0,
        `isPrivate` BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE
        )';
    try {
        $db->query($createSQL);
    } catch (Exception $ex) {
        LOG_M('Create table uploads failed: ', $ex->getMessage());
        die();
    }
};

function DBOcreateTableLikes() {
    $db = DBOconnect();
    $createSQL = 'CREATE TABLE IF NOT EXISTS `likes` 
        (`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `userid` INT(5) UNSIGNED NOT NULL,
        `imgid` INT(10) UNSIGNED NOT NULL,
        FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`imgid`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
        )';
    $uniqueSQL = 'ALTER TABLE likes ADD CONSTRAINT UNIQUE_userid_imgid UNIQUE CLUSTERED ( userid, imgid )';
    try {
        $db->query($createSQL);
        $db->query($uniqueSQL);
    } catch (Exception $ex) {
        LOG_M('Create table uploads failed: ', $ex->getMessage());
        die();
    }
};

function DBOcreateTableComments() {
    $db = DBOconnect();
    $createSQL = 'CREATE TABLE IF NOT EXISTS `comments` 
        (`id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
        `message` VARCHAR(2048) NOT NULL,
        `userid` INT(5) UNSIGNED NOT NULL,
        `imgid` INT(10) UNSIGNED NOT NULL,
        FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`imgid`) REFERENCES `uploads` (`id`) ON DELETE CASCADE
        )';
    try {
        $db->query($createSQL);
    } catch (Exception $ex) {
        LOG_M('Create table comments failed: ', $ex->getMessage());
        die();
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
    return false;
};

function DBOdeleteUserPictures($username)
{
    // prepare and delete all the user pictures
    return true;
}

function DBOdeleteAccount($username)
{
    $db = DBOconnect();
    DBOdeleteUserPictures($username);
    $stmt = $db->prepare('DELETE FROM `users` WHERE `name`=?');
    return $stmt->execute([$username]);
}

function DBOremovePicture($imgid)
{
    $db = DBOconnect();
    $stmt = $db->prepare('DELETE FROM `uploads` WHERE `id`=?');
    return $stmt->execute([$imgid]);
}

function DBOinsertUpload($name, $user)
{
    $db = DBOconnect();
    $user = DBOselectUser($user);
    if (isset($user['id'])) {
        $stmt = $db->prepare('INSERT INTO `uploads` (`name`, `userid`) VALUES (?, ?)');
        try {
            return $stmt->execute([$name, $user['id']]);
        } catch (Exception $e) {
            LOG_M('SQL Error: '.$e->getMessage());
            return false;
        }
    }
    return false;
}
/**
 * Searches for all the uploaded pictures
 * Number of results could be limited using 'offset' and 'limit' params
 * 'filter' array used to filter results based on 'col' and 'value'
 * !!! when filter is used, 'value' param is mandatory !!!
 * Ordering: 'orderby' and 'order'
 * 
 * @param $params['offset'] int - offset for limit, def 0
 * @param $params['limit'] int - max limit for limit, def 20
 * @param $params['filter'] array - filter:
 *  @param $params['filter']['table'] - table name, def 'up'
 *  @param $params['filter']['col'] - column name, def 'name'
 *  @param $params['filter']['value'] - value, MANDATORY
 * @param $params['orderby'] string - column name for sorting, def 'id'
 * @param $params['order'] DESC/ASC - sorting direction, def 'DESC'
 */

function DBOselectUploads($params=null)
{
    $db = DBOconnect();
    // LOG_M('params', $params);
    $offset = $params['offset'] ?? 0;
    $limit = $params['limit'] ?? 20;
    $filter = $params['filter'] ?? null;
    $orderby = $params['orderby'] ?? 'id';
    $order = $params['order'] ?? 'DESC';
    if ($filter) {
        $table = $filter['table'] ?? 'up';
        $col = $filter['col'] ?? 'name';
        if (!isset($filter['value'])) {
            die('Value is not set when filter uploads');
        }
        $value = $filter['value'];
        $stmt = $db->prepare("SELECT us.name `user`, up.id, up.name, up.rating, up.isPrivate FROM `users` us JOIN `uploads` up ON us.id=up.userid WHERE {$table}.{$col}='{$value}' ORDER BY up.{$orderby} {$order} LIMIT {$offset}, {$limit}");
    } else {
        $stmt = $db->prepare("SELECT us.name `user`, up.id, up.name, up.rating, up.isPrivate FROM `users` us JOIN `uploads` up ON us.id=up.userid ORDER BY up.{$orderby} {$order} LIMIT {$offset}, {$limit}");
    }
    // LOG_M($stmt->queryString);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}

function DBOselectAllUploads($user, $params=null)
{
    $db = DBOconnect();
    // LOG_M('params', $params);
    $offset = $params['offset'] ?? 0;
    $limit = $params['limit'] ?? 20;
    $filter = $params['filter'] ?? null;
    $orderby = $params['orderby'] ?? 'id';
    $order = $params['order'] ?? 'DESC';
    $stmt = $db->prepare(<<<EOL
    SELECT us.name `user`, up.id, up.name, l.imgid, up.rating, up.isPrivate FROM `users` us 
    JOIN `uploads` up ON us.id=up.userid 
    LEFT JOIN (SELECT * FROM `likes` WHERE userid in (SELECT `id` from `users` WHERE `name`='{$user}')) l ON l.imgid=up.id
    WHERE (us.name<>'{$user}' AND up.isPrivate=0) OR us.name='{$user}'
    ORDER BY up.{$orderby} {$order} LIMIT {$offset}, {$limit}
    EOL);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}

function DBOgetGallerySize($user)
{
    $db = DBOconnect();
    $stmt = $db->prepare("SELECT COUNT(id) FROM `uploads` WHERE `userid` in (SELECT `id` from `users` WHERE `name`='?') OR  `isPrivate`='false'");
    $stmt->execute([$user]);
    $res = $stmt->fetch(PDO::FETCH_COLUMN, 0);
    return $res;
}

function DBOinsertLike($user, $imgid)
{
    $db = DBOconnect();
    $user = DBOselectUser($user);
    if (isset($user['id'])) {
        $stmt = $db->prepare('INSERT INTO `likes` (`userid`, `imgid`) VALUES (?, ?)');
        try {
            $res = $stmt->execute([$user['id'], $imgid]);
            return $res;
        } catch (Exception $e) {
            // LOG_M('SQL Error: '.$e->getMessage());
            return false;
        }
    }
    return false;
}

function DBOremoveLike($user, $imgid)
{
    $db = DBOconnect();
    $user = DBOselectUser($user);
    if (isset($user['id'])) {
        $stmt = $db->prepare('DELETE FROM `likes` WHERE `userid`=? AND `imgid` =?');
        try {
            return $stmt->execute([$user['id'], $imgid]);
        } catch (Exception $e) {
            // LOG_M('SQL Error: '.$e->getMessage());
            return false;
        }
    }
    return false;
}

function DBOupdatePrivacy($imgid, $isPrivate)
{
    $db = DBOconnect();
    $stmt = $db->prepare("UPDATE `uploads` SET `isPrivate`='{$isPrivate}' WHERE `id`={$imgid}");
    try {
        $res = $stmt->execute([$isPrivate, $imgid]);
        return $res;
    } catch (Exception $e) {
        // LOG_M('SQL Error: '.$e->getMessage());
        return false;
    }
    return false;
}

function DBOselectLikes(array $imgs, $user)
{
    $db = DBOconnect();
    $images = [];
    foreach ($imgs as $item => $row) {
        $images[] = $row['id'];
    }
    $images = implode(',', $images);
    $stmt = $db->prepare("SELECT imgid FROM likes WHERE userid in (SELECT id from users WHERE name='{$user}') AND imgid IN ({$images})");
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    return $res;
}

function DBOaddComment($message, $author, $imgid)
{
    $db = DBOconnect();
    $user = DBOselectUser($author);
    if (isset($user['id'])) {
        $stmt = $db->prepare('INSERT INTO `comments` (`message`, `userid`, `imgid`) VALUES (?, ?, ?)');
        try {
            $res = $stmt->execute([$message, $user['id'], $imgid]);
            return $res;
        } catch (Exception $e) {
            // LOG_M('SQL Error: '.$e->getMessage());
            return false;
        }
    }
    return false;
}

function DBOdisconnect() {
    $db = &$GLOBALS['db'];
    $db = null;
};

function DBOcountRows($table)
{
    $db = DBOconnect();
    $stmt = $db->prepare("SELECT COUNT(`id`) FROM {$table}");
    try {
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $res[0];
    } catch (Exception $e) {
        return false;
    }
}

function prepareDB()
{
    $db = DBOconnect();
    if (!DBOcountRows('users')) DBOcreateTableUsers();
    if (!DBOcountRows('snippets')) DBOcreateTableSnippets();
    if (!DBOcountRows('uploads')) DBOcreateTableUploads();
    if (!DBOcountRows('likes')) DBOcreateTableLikes();
    if (!DBOcountRows('comments')) DBOcreateTableComments();
}

prepareDB();
?>