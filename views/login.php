<form name="login" id="login" action="config/users.php" onsubmit="return validateLoginForm()" method="post">
    <input type="text" name="username" value="" id="username" placeholder="Your username, please" />
    <input type="password" name="password" value="" id="password" placeholder="And a password" />
    <input class="button accent" type="submit" name="submit" value="Login" />
</form>
<div class="section_form">
    <span>Nor yet in a club?</span>
    <button class="button" onclick="register()">Register</button>
</div>