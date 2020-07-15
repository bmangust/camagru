<form name="register" id="register" action="api/users.php" onsubmit="return validateRegisterForm()" method="post">
    <input type="email" name="email" value="" id="email" placeholder="Your email here, please" />
    <input type="text" name="username" value="" id="username" placeholder="And your username" />
    <input type="password" name="password" value="" id="password" placeholder="And a password" />
    <input type="password" name="confirm" value="" id="confirm" placeholder="Confirm password" />
    <input class="button accent" type="submit" name="submit" value="Register" />
</form>
<div class="section_form">
    <button class="button" onclick="login()">Login</button>
</div>