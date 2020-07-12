<span>Enter your email and we'll send you a link to restore the password</span>
<form name="forgot" id="forgot" action="config/users.php" onsubmit="return validateForgotForm()" method="post">
    <input type="email" name="email" value="" id="email" required placeholder="Your email, please" />
    <input class="button accent" type="submit" name="submit" value="Restore password" />
</form>
<div class="section_form">
    <button class="button" onclick="login()">Login</button>
</div>