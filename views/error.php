<div class="float <?=$_SERVER['class']?>">
    <?php foreach($_SERVER['msg'] as $msg) {
        echo "<div>$msg</div>";
    }
    unset($_SESSION['msg']);
    unset($_SESSION['class']);
    ?>
</div>