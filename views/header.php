<nav>
    <div class="nav__burger" id="hamburger">
        <div class="hamburger"></div>
    </div>
    <ul class="row nav">
        <li>
            <a class="button nav__item" href="./index.php?route=menu">home</a>
        </li>
        <li>
            <a class="button nav__item" href="./index.php?route=gallery">gallery</a>
        </li>
        <?php if(isset($_SESSION['user']) && $_SESSION['is_auth'] === true): ?>
            <li>
                <a class="button nav__item" href="./index.php?route=settings">settings</a>
            </li>
            <li>
                <a class="button nav__item" href="./api/users.php?action=logout">logout</a>
            </li>
        <?php else: ?>
            <li>
                <a class="button nav__item" href="./index.php?route=login">login</a>
            </li>
        <?php endif ?>
    </ul>
</nav>
<h1>
    <?=$_SERVER['header']?>
</h1>
