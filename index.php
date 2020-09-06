<?php
session_start();
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, 'api', 'globals.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, 'classes', 'Logger.class.php'));
$header = './views/header.php';
$main = './views/menu.php';
$footer = './views/footer.php';
$error = null;
$path = explode(DIRECTORY_SEPARATOR, __DIR__);
$GLOBALS['path'] = $path[count($path) - 1];

if (isset($_SESSION['user']) && ($user = DBOselectUser($_SESSION['user'])) ) {
    setcookie('user', $_SESSION['user']);
} else {
    $_SESSION['user'] = false;
    $_SESSION['is_auth'] = false;
}
Logger::Dlog (['function' => __FILE__.__FUNCTION__, 'line' => __LINE__, 'descr' => 'user', 'message' => $user ?? null]);
Logger::Dlog (['function' => __FILE__.__FUNCTION__, 'line' => __LINE__, 'descr' => 'session', 'message' => $_SESSION]);

$_SERVER['header'] = "Welcome to CAMAGRU";
if (isset($_SESSION['user']) && $_SESSION['user'] !== FALSE && isset($_SESSION['is_auth']) && $_SESSION['is_auth'] !== FALSE) {
    $_SERVER['header'] = $_SERVER['header'] . ", " . $_SESSION['user'];
}

if ($_GET) {
    if (isset($_SESSION['msg'])) {
        $_SERVER['msg'] = $_SESSION['msg'];
        $_SERVER['class'] = @$_SESSION['class'] ?? "";
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
                $_SERVER['header'] = "Get your new password here";
                $main = './views/restore.php';
                break;
            case 'update_email':
                $_SERVER['header'] = "Update your email";
                $main = './views/update_email.php';
                break;
            case 'update_username':
                $_SERVER['header'] = "Update your username";
                $main = './views/update_username.php';
                break;

            case 'create':
                $_SERVER['header'] = "Let's create some stuff!";
                $main = './views/create.php';
                break;
            case 'settings':
                $_SERVER['header'] = $_SESSION['user']."'s settings";
                $main = './views/settings.php';
                break;
            case 'profile':
                if (isset($_GET['user'])) {
                    $_SERVER['header'] = $_GET['user']."'s public gallery";
                    $main = './views/profile.php';
                    break;
                } else {
                    header("Location: ./index.php?route=menu");
                    break;
                }
            case 'gallery':
                $_SERVER['header'] = "Gallery";
                $main = './views/gallery.php';
                break;
            case 'image':
                $_SERVER['header'] = "View image";
                $main = './views/image.php';
                break;
            case 'main':
                $main = './views/main.php';
                break;
            default:
                header("Location: ./index.php?route=menu");
        }
    }
}
?>
<?php include './views/main.php';?>