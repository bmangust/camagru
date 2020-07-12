<?php
session_start();
require_once 'config/setup.php';
require_once 'log.php';
$header = './views/header.php';
$main = './views/menu.php';
$footer = './views/footer.php';
$error = null;

LOG_M("session", $_SESSION);
$_SERVER['header'] = "Welcome to CAMAGRU";

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
                $_SERVER['header'] = "Are you with us?";
                $main = './views/login.php';
                break;
            case 'register':
                $_SERVER['header'] = "Come join us!";
                $main = './views/register.php';
                break;
            case 'forgot':
                $_SERVER['header'] = "We'll help you!";
                $main = './views/forgot.php';
                break;
            case 'restore':
                $_SERVER['header'] = "Come join us!";
                $main = './views/restore.php';
                break;
            case 'create':
                $_SERVER['header'] = "Let's create some stuff!";
                $main = './views/create.php';
                break;
            case 'settings':
                $_SERVER['header'] = "Settings";
                $main = './views/settings.php';
                break;
            case 'gallery':
                $_SERVER['header'] = "Gallery";
                $main = './views/gallery.php';
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