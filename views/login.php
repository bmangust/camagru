<?php
if (isset($_SESSION['is_auth']) && $_SESSION['is_auth'] != FALSE) {
    header("Location: ./index.php?route=menu");
}?>
<form name="login" id="login" action="api/users.php" onsubmit="return validateLoginForm()" method="post">
    <!-- <input type="text" name="username" value=<?=$_SESSION['user'] ?? ""?> id="username" placeholder="Your username, please" /> -->
    <input type="text" name="username" value="" id="username" placeholder="Your username, please" />
    <input type="password" name="password" value="" id="password" placeholder="And a password" />
    <input class="button accent" type="submit" name="submit" value="Login" />
</form>
<div class="section_form">
    <span>Nor yet in a club?</span>
    <button class="button" onclick="register()">Register</button>
    <button class="button" onclick="forgot()">Forgot password?</button>
</div>