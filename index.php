<?php
session_start();
require_once 'config/setup.php';
require_once 'log.php';
$header = './views/header.php';
$main = './views/menu.php';
$footer = './views/footer.php';
$error = null;

if ($_GET) {
    if (isset($_GET['msg'])) {
        $msg = getMessage($_GET['msg']);
        $_SERVER['msg'] = $msg;
        $_SERVER['class'] = $_GET['class'];
        $error = "./views/error.php";
    }
    if (isset($_GET['route'])) {
        switch ($_GET['route']) {
            case 'menu':
                $main = './views/menu.php';
                break;
            case 'login':
                $main = './views/login.php';
                break;
            case 'create':
                $main = './views/create.php';
                break;
            case 'settings':
                $main = './views/settings.php';
                break;
            case 'gallery':
                $createTableUsers();
                // $main = './views/gallery.php';
                break;
            case 'main':
                $main = './views/main.php';
                break;
            default:
                $main = './views/menu.php';
        }
    }
}
?>
<?php include './views/main.php';?>