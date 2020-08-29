<?php
if ($_SESSION['is_auth'] == FALSE) {
    header("Location: ./index.php?route=menu");
}?>
<ul>
        <li>
            <input id="notificationsSwitcher" name="notificationsSwitcher" type="checkbox" onchange="updateNotifications(this)"/>
            <label for="notificationsSwitcher">Enable email notificatons</label>
        </li>
        <li>
            <button class="button menuItem" onclick="changeUserAvatar()">Change my avatar</button>
        </li>
        <li>
            <button class="button menuItem" onclick="showGallery()">Show my gallery</button>
        </li>
        <li>
            <a class="button menuItem" href="./api/users.php?action=<?=$GLOBALS['ACTION_UPDATE_PASS']?>">change password</a>
        </li>
        <li>
            <a class="button menuItem" href="./api/users.php?action=<?=$GLOBALS['ACTION_UPDATE_EMAIL']?>">change email</a>
        </li>
        <li>
            <a class="button menuItem" href="./api/users.php?action=<?=$GLOBALS['ACTION_UPDATE_USERNAME']?>">change username</a>
        </li>
        <li>
            <a class="button menuItem" onclick="confirmDelete()" href="#">delete account</a>
        </li>
</ul>