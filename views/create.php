<?php
if (!isset($_SESSION['is_auth']) || !isset($_SESSION['user']) || $_SESSION['is_auth'] != true) {
    $_SERVER['msg'] = "Authorized persons only. Please, log in";
    $_SERVER['class'] = "error";
    include "./views/error.php";
} else {?>
<div class="createView">
    <?php include 'editor.php'; ?>
    <aside class="preview">
        <?php include 'gallery.php'; ?>
    </aside>
</div>
<?php }