<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.12.17
 * Time: 16:41
 */

require 'libs/rb.php';

$dbname = 'users';
$user = 'root'; //user for connection to db
$password = '123'; //password for connection to db

R::setup('mysql:host=localhost;dbname='.$dbname,
    $user, $password);

session_start();