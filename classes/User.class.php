<?php
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'config', 'setup.php'));
require_once join(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'log.php'));

class User {
    // check if user entered valid password
    public static function authorize($user, $pwd) {
        $userRes = DBOselectUser($user);
        if (isset($userRes['name'])) {
            return hash('whirlpool', $pwd) === $userRes['password'];
        }
        return false;
    }

    // check if user confirmed email
    public static function checkUserVerification($username) {
        $user = DBOselectUser($username);
        if ($user && $user['verified'] == true) {
            return true;
        }
        return false;
    }

    public static function sendEmail($userEmail, array $message) {
        global $enable_debug;
        $url = 'https://api.elasticemail.com/v2/email/send';

        try{
                $post = array('from' => 'ftf.pollux@gmail.com',
                'fromName' => 'Akraig camagru',
                'apikey' => $GLOBALS['ELASTIC_EMAIL_API_KEY'],
                'subject' => $message['title'],
                'to' => $userEmail,
                'bodyHtml' => '<h3>'.$message['body'].'</h3>',
                'isTransactional' => false);
                
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ));
                
                $res = json_decode(curl_exec ($ch), true);
                curl_close ($ch);
                LOG_M($res);
                return $res['success'];
        }
        catch(Exception $ex){
            LOG_M($ex->getMessage());
            return false;
        }
    }

    public static function registerUser($username, $email, $password)
    {
        $user = DBOselectUser($_POST['email']);
        $_SESSION['is_auth'] = false;
        if (!isset($user['email'])) {
            $email = strtolower($_POST['email']);
            $pwd = hash('whirlpool', $_POST['password']);
            if(!insertUser($_POST['username'], $email, $pwd)) {
                unset($_SESSION['user']);
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'This username has already been taken';
                header("Location: ../index.php?route=register");
            }
            $bytes = random_bytes(5);
            $code = bin2hex($bytes);
            $message = [];
            $message['title'] = "Activate account";
            $message['body'] = "Activate account link: http://localhost/camagru/api/users.php?action={$GLOBALS['ACTION_ACTIVATE']}&code={$code}&email={$email}";
            setUserCode($code, $_POST['username'], $email);
            if (!User::sendEmail($email, $message)) {
                unset($_SESSION['user']);
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'Server error, please try again';
                header("Location: ../index.php?route=register");
            }
            // $_SESSION['user'] = $_POST['username'];
            $_SESSION['msg'][] = 'Check your email';
            header("Location: ../index.php?route=login");
        } else {
            unset($_SESSION['user']);
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'This email has already been taken';
            header("Location: ../index.php?route=register");
        }
    }

    public static function userLogin($username, $password)
    {
        if (User::authorize($username, $password)) {
            if (!User::checkUserVerification($username)) {
                unset($_SESSION['user']);
                $_SESSION['is_auth'] = false;
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'Email is not confirmed';
                header("Location: ../index.php?route=login");
            }
            $_SESSION['user'] = $username;
            $_SESSION['is_auth'] = true;
            header("Location: ../index.php?route=menu");
        } else {
            unset($_SESSION['user']);
            $_SESSION['is_auth'] = false;
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Username or password in wrong';
            header("Location: ../index.php?route=login");
        }
    }

    public static function restorePassword($email)
    {
        $user = DBOselectUser($email);
        LOG_M('user', $user);
        if ($user) {
            $bytes = random_bytes(5);
            $code = bin2hex($bytes);
            $message = [];
            $message['title'] = "Restore password";
            $message['body'] = "Restore password link: http://localhost/camagru/api/users.php?action={$GLOBALS['ACTION_RESTORE']}&code={$code}&email={$email}";
            setUserCode($code, $user['name'], $email);
            if (!User::sendEmail($email, $message)) {
                unset($_SESSION['user']);
                $_SESSION['class'] = 'error';
                $_SESSION['msg'][] = 'Server error, please try again';
                header("Location: ../index.php?route=forgot");
            }
            $_SESSION['msg'][] = 'Check your email';
            header("Location: ../index.php?route=login");
        } else {
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Email not found';
            header("Location: ../index.php?route=forgot");
        }
    }

    public static function savePassword($email, $password)
    {
        $user = DBOselectUser($email);
        LOG_M('user', $user);
        if ($user) {
            DBOupdatePassword($user['email'], hash('whirlpool', $password));
            $_SESSION['msg'][] = 'Your new password saved';
            header("Location: ../index.php?route=login");
        } else {
            $_SESSION['class'] = 'error';
            $_SESSION['msg'][] = 'Email not found';
            header("Location: ../index.php?route=restore");
        }
    }
}