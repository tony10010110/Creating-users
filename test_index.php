<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.12.17
 * Time: 17:17
 */

//require 'db.php';

$data = $_POST;

if (!isset($_SESSION['logged_user']))
{
    header('Location: /');
}

if (isset($data['do_creating']))
{
    //тут виконується перевірка на створення користувача

    $error = []; //масив який буде збрерігати помилки
    $data['password'] = passGenerate(); //генерація паролю

    if (trim($data['name']) == '') //перевірка на пусте поле
    {
        $error[] = 'Введіть імя користувача';
    }

    if (trim($data['login']) == '') //перевірка на пусте поле
    {
        $error[] = 'Введіть логін користувача';

//        if (R::count('users', 'login = ?', [$data['login']]) > 0) //перевірка чи такий логін вже існує
//        {
//            $error[] = 'Користувач з таким логіном вже існує';
//        }
    }


    if (trim($data['email']) == '') //перевірка на пусте поле
    {
        $error[] = 'Введіть email користувача';

//        if (R::count('users', 'email = ?', [$data['email']]) > 0) //перевірка чи такий email вже існує
//        {
//            $error[] = 'Користувач з таким email існує';
//        }
    }

    if (empty($error)) //якщо помилок немає
    {
//        $user = R::dispense('users');
        $user->login = $data['login'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);

        $data_email = [
            'email' => $data['email'],
            'login' => $data['login'],
            'password' => $data['password']
        ];

        $result_sending = sendMessage($data_email); //результат відправлення листа
//        R::store($user); //збереження в БД

        $notice = '<p id="result" style="color: green">Користувач доданий в базу даних</p>';

        if ($result_sending){
            $notice .= '<p id="result_sending" style="color: red">Лист не було відправлено</p>';
        } else {
            $notice .= '<p id="result_sending" style="color: green">Лист було успішно відправлено</p>';
        }

        $notice .= '<hr>';

        echo $notice;


    } else //вивід помилки
    {
        echo '<p id="errors" style="color: red">'.array_shift($error).'</p><hr>';
    }
}


function passGenerate() { //генерація паролю
    $s = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $l = 8;
    $r = '';
    for($i = 0; $i < $l; $i++)
        $r .= $s[rand(0, strlen($s) - 1)];
    return $r;
}

function sendMessage($data) {
    $to = $data['email'];
    $subject = 'Заголовок листа';
    $message = '                             
        <p><strong>Ваш логін:</strong>'.$data['login'].'</p>
        <p><strong>Ваш пароль:</strong>'.$data['password'].'</p>
    ';

    $headers = 'Content-type: text/html; charset=windows-1251' . "\r\n";
    $headers .= 'From: examle@gmail.com';

    $result = mail($to, $subject, $message, $headers);

    return $result;
}

?>

<a href="/logout.php">Вийти...</a>

<form action="/creating_users.php" method="post">

    <p>
    <p><strong>Імя користувача</strong></p>
    <input type="text" name="name" value="<?= @$data['name']; ?>">
    </p>

    <p>
    <p><strong>Логін користувача</strong></p>
    <input type="text" name="login" value="<?= @$data['login'];?>">
    </p>

    <p>
    <p><strong>Email користувача</strong></p>
    <input type="email" name="email" value="<?= @$data['email'] ?>">
    </p>

    <p>
        <button type="submit" name="do_creating">Зареєструвати</button>
    </p>

</form>


