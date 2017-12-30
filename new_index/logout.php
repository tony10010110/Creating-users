<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.12.17
 * Time: 23:55
 */

session_start();

unset($_SESSION['logged_user']);
header('Location: /new_index/index.php');
