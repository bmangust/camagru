<div>
    <ul>
        <li>
            <a class="button menuItem" href="./index.php?route=gallery">gallery</a>
        </li>
        <?php if(isset($_SESSION['user']) && isset($_SESSION['is_auth']) && $_SESSION['is_auth'] === true) { ?>
            <li>
                <a class="button menuItem" href="./index.php?route=create">create</a>
            </li>
        <?php } else { ?>
            <li>
                <a class="button menuItem" href="./index.php?route=login">login</a>
            </li>
        <?php } ?>
    </ul>
</div> 