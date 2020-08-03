<form name="restore" id="restorePassword" action="api/users.php" onsubmit="return validateRestoreForm()" method="post">
    <input type="hidden" name="email" value="<?=$user['email']?>"/>
    <input type="password" name="password" value="" id="password" required placeholder="Enter new password" />
    <input type="password" name="confirm" value="" id="confirm" required placeholder="Confirm new password" />
    <input class="button accent" type="submit" name="submit" value="Save" />
</form>
<div class="section_form">
    <button class="button" onclick="login()">Login</button>
</div>