<?php
if ($_SESSION['is_auth'] == FALSE) {
    header("Location: ./index.php?route=menu");
}
$avatar = $user['avatar'] ? $user['avatar'].'.png' : 'default.png';
$avatar_path = join(DIRECTORY_SEPARATOR, ['assets', 'avatars', $avatar]);
$user_info = $user['info'] ?? 'No info';
?>
<div class="settings">
<div class="settings__info">
    <div class="settings__avatar">
        <img id="avatar" class="avatar" src="<?=$avatar_path?>" alt="<?=$_SESSION['user']?> avatar"/>
        <div class="settings__upload-button">
            <input type="file" name="avatar" id="file" class="input-file">
            <label for="file">
                <svg class="icon" viewBox="0 0 484 484">
                    <path fill="#fff" d="m 401.648 18.2344 c -24.3945 -24.3516 -63.8984 -24.3516 -88.293 0 l -22.1016 22.2227 l -235.27 235.145 l -0.5 0.503907 c -0.121094 0.121093 -0.121094 0.25 -0.25 0.25 c -0.25 0.375 -0.625 0.746093 -0.871094 1.12109 c 0 0.125 -0.128906 0.125 -0.128906 0.25 c -0.25 0.375 -0.371094 0.625 -0.625 1 c -0.121094 0.125 -0.121094 0.246094 -0.246094 0.375 c -0.125 0.375 -0.25 0.625 -0.378906 1 c 0 0.121094 -0.121094 0.121094 -0.121094 0.25 l -52.1992 156.969 c -1.53125 4.46875 -0.367187 9.41797 2.99609 12.7344 c 2.36328 2.33203 5.55078 3.63672 8.86719 3.625 c 1.35547 -0.023438 2.69922 -0.234376 3.99609 -0.625 l 156.848 -52.3242 c 0.121094 0 0.121094 0 0.25 -0.121094 c 0.394531 -0.117187 0.773437 -0.285156 1.12109 -0.503906 c 0.097656 -0.011719 0.183593 -0.054688 0.253906 -0.121094 c 0.371094 -0.25 0.871094 -0.503906 1.24609 -0.753906 c 0.371093 -0.246094 0.75 -0.621094 1.125 -0.871094 c 0.125 -0.128906 0.246093 -0.128906 0.246093 -0.25 c 0.128907 -0.125 0.378907 -0.246094 0.503907 -0.5 l 257.371 -257.371 c 24.3516 -24.3945 24.3516 -63.8984 0 -88.2891 Z m -232.273 353.148 l -86.9141 -86.9102 l 217.535 -217.535 l 86.9141 86.9102 Z m -99.1563 -63.8086 l 75.9297 75.9258 l -114.016 37.9609 Z m 347.664 -184.82 l -13.2383 13.3633 l -86.918 -86.918 l 13.3672 -13.3594 c 14.6211 -14.6094 38.3203 -14.6094 52.9453 0 l 33.9648 33.9648 c 14.5117 14.6875 14.457 38.332 -0.121094 52.9492 Z m 0 0"></path>
                </svg>
                <span>Upload picture</span>
            </label>
            
        </div>
    </div>
    <div class="settings__username"><?=$user['name']?></div>
    <div class="settings__profile-info"><?=$user_info?></div>
    <button id="edit" class="button" onclick="editProfile(event)">Edit profile</button>
</div>
<ul class="settings__controls">
        <li>
            <input id="notificationsSwitcher" name="notificationsSwitcher" type="checkbox" onchange="updateNotifications(this)"/>
            <label for="notificationsSwitcher">Enable email notificatons</label>
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
            <a class="button menuItem" onclick="confirmDelete()" href="#">delete account</a>
        </li>
</ul>
</div>