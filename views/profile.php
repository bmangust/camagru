<?php
if ($_SESSION['is_auth'] == FALSE) {
    header("Location: ./index.php?route=menu");
}?>
<div>
    <ul>
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
                    <!-- ./api/users.php?action=<?=$GLOBALS['ACTION_DELETE_ACCOUNT']?> -->
            </li>
    </ul>
</div> 