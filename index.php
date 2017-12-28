<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.12.17
 * Time: 16:33
 */

require 'db.php';

if (isset($_SESSION['logged_user']))
{
    header('Location: /creating_users.php');
} else {
    echo '<p>Ви не авторизовані! <br> Для продовження роботи потрібно <a href="/login.php">авторизуватись</a></p>';
}

