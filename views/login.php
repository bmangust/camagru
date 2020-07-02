<form name="login" id="login" action="config/users.php" onsubmit="return validateLoginForm()" method="post">
    <input type="text" name="username" value="" id="username" placeholder="Your username, please" />
    <input type="password" name="password" value="" id="password" placeholder="And a password" />
    <input type="submit" name="submit" value="Login" />
</form>
<div class="section_register">
    <span>Nor yet in a club?</span>
    <button class="button accent" onclick="register()">Register</button>
</div>