<?php
if (!isset($_SESSION['is_auth']) || !isset($_SESSION['user']) || $_SESSION['is_auth'] != true) {
    $_SESSION['msg'][] = "Authorized persons only. Please, log in";
    $_SESSION['class'] = "error";
    header("Location: ./index.php?route=menu");
} else {?>
<div class="createView">
    <?php include 'editor.php'; ?>
    <aside class="preview">
        <?php include 'gallery.php'; ?>
    </aside>
</div>
<?php }