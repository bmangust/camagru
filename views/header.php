<nav>
    <ul class="row nav">
        <li>
            <a class="button navItem" href="./index.php?route=menu">home</a>
        </li>
        <li>
            <a class="button navItem" href="./index.php?route=gallery">gallery</a>
        </li>
        <?php if(isset($_SESSION['user']) && $_SESSION['is_auth'] === true) { ?>
            <li>
                <a class="button navItem" href="./index.php?route=settings">settings</a>
            </li>
            <li>
                <a class="button navItem" href="./api/users.php?action=logout">logout</a>
            </li>
        <?php if ($user['isAdmin']) { ?>
            <li>
                <a class="button navItem" href="./index.php?route=admin_panel">Administration</a>
            </li>
           <?php }
         } else { ?>
            <li>
                <a class="button navItem" href="./index.php?route=login">login</a>
            </li>
        <?php } ?>
    </ul>
</nav>
<h1>
    <?=$_SERVER['header']?>
</h1>
