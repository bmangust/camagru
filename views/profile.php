<div>
    <ul>
        <?php if(isset($_SESSION['user']) && $_SESSION['is_auth'] === true) { ?>
            <li>
                <a class="button menuItem" href="./api/users.php?action=<?=$GLOBALS['ACTION_UPDATE_PASS']?>">change password</a>
            </li>
            <li>
                <a class="button menuItem" href="./api/users.php?action=change_email">change email</a>
            </li>
            <li>
                <a class="button menuItem" href="./api/users.php?action=change_username">change username</a>
            </li>
            <li>
                <a class="button menuItem" href="./api/users.php?action=delete_account">delete account</a>
            </li>
        <?php } ?>
    </ul>
</div> 