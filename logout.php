<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.12.17
 * Time: 23:55
 */

require 'db.php';
unset($_SESSION['logged_user']);
header('Location: /');