<?php
if ($_SESSION['is_auth'] == FALSE) {
    header("Location: ./index.php?route=menu");
}?>
<form name="update_username" id="update_username" action="api/users.php" method="post">
    <input type="hidden" name="email" value="<?=$user['email']?>"/>
    <input type="text" name="username" value="" id="username" placeholder="Your new username" required minlength="3" maxlength="25"/>
    <input class="button accent" type="submit" name="submit" value="Update username" />
</form>