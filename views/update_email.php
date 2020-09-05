<?php
if ($_SESSION['is_auth'] == FALSE) {
    header("Location: ./index.php?route=menu");
}?>
<form name="update_email" id="update_email" action="api/users.php" onsubmit="return validateUpdateEmail()" method="post">
    <input type="hidden" name="email" value="<?=$user['email']?>"/>
    <input type="email" name="newEmail" value="" id="newEmail" placeholder="Your new email" />
    <input class="button accent" type="submit" name="submit" value="Update email" />
</form>