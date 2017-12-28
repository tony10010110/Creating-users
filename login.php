<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.12.17
 * Time: 16:37
 */

require 'db.php';

/***************** ADMIN *************************/
$login = 'user';
$password = 'user';
/***************** ADMIN *************************/


$data = $_POST;

if (isset($data['do_login'])) //якщо поле заповнене і нажата кнопка входу
{
    $error = [];

    if ($data['login'] == $login && $data['password'] == $password)
    {
        $_SESSION['logged_user'] = $data['login'];
        header('Location: /creating_users.php');

    } else
    {
        $error[] = 'Не вірний логін, або пароль';
    }
}

if (!empty($error))
{
    echo '<div style="color: red">'.array_shift($error).'</div>';
}

?>

<form action="/login.php" method="post">
    <p>
        <input type="text" name="login" placeholder="login...">
    </p>

    <p>
        <input type="password" name="password" placeholder="password...">
    </p>

    <p>
        <button type="submit" name="do_login">Вхід</button>
    </p>
</form>
