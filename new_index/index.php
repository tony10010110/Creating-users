<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 30.12.17
 * Time: 18:49
 */

session_start();

if (isset($_SESSION['logged_user'])) {
    //виконати створення користувача
    echo '
            <strong style="padding-left: 60pt">hello, admin!</strong>
            <a href="/new_index/logout.php">Вийти...</a>
            <hr>';
    create_user();
} else {
    //авторизація
    authorization();
}


function authorization()
{
    /***************** ADMIN *************************/
    $login = 'user';
    $password = 'user';
    /***************** ADMIN *************************/

    $data = $_POST;

    if (isset($data['do_login'])) //якщо поле заповнене і нажата кнопка входу
    {
        $error = [];

        if ($data['login'] == $login && $data['password'] == $password){
            $_SESSION['logged_user'] = $data['login'];
            header('Location: index.php');
        } else {
            $error[] = 'Не вірний логін, або пароль';
        }
    }

    if (!empty($error)) {
        echo '<div style="color: red">'.array_shift($error).'</div><hr>';
    }

    echo '
        <form action="/new_index/index.php" method="post">
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
    ';
}



function create_user()
{


    $data = $_POST;

    if (isset($data['do_creating']))
    {
        //тут виконується перевірка на створення користувача

        $error = []; //масив який буде збрерігати помилки
        $data['password'] = password_generate(); //генерація паролю

        if (trim($data['name']) == '') //перевірка на пусте поле
        {
            $error[] = 'Введіть імя користувача';
        }

        if (trim($data['login']) == '') //перевірка на пусте поле
        {
            $error[] = 'Введіть логін користувача';

        } elseif (count_users_by_login($data['login']) > 0) { //перевірка чи такий логін вже існує
            $error[] = 'Користувач з таким логіном вже існує';
        }


        if (trim($data['email']) == '') //перевірка на пусте поле
        {
            $error[] = 'Введіть email користувача';

        } elseif(count_users_by_email($data['email']) > 0) { //перевірка чи такий email вже існує
            $error[] = 'Користувач з таким email існує';
        }

        if (empty($error)) //якщо помилок немає
        {
            $data_email = [
                'email' => $data['email'],
                'login' => $data['login'],
                'password' => $data['password']
            ];

            $result_sending = send_message($data_email); //результат відправлення листа

            $result_seting = set_user($data['login'], $data['email'], $data['password']); //збереження в БД

            $notice = '';

            if ($result_seting == false) {
                $notice .= '<p id="result" style="color: red">Користувач не був доданий в базу даних</p>';
            } else {
                $notice .= '<p id="result" style="color: green">Користувач доданий в базу даних</p>';
            }



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

    echo '    
        <form action="/new_index/index.php" method="post">
        
            <p>
                <p><strong>Імя користувача</strong></p>
                <input type="text" name="name" value="'. @$data['name'] .'">
            </p>
        
            <p>
                <p><strong>Логін користувача</strong></p>
                <input type="text" name="login" value="'. @$data['login'] .'">
            </p>
        
            <p>
                <p><strong>Email користувача</strong></p>
                <input type="email" name="email" value="'. @$data['email'] .'">
            </p>
        
            <p>
                <button type="submit" name="do_creating">Зареєструвати</button>
            </p>
        
        </form>
    ';


}


function password_generate() { //генерація паролю
    $s = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $l = 8;
    $r = '';
    for($i = 0; $i < $l; $i++)
        $r .= $s[rand(0, strlen($s) - 1)];
    return $r;
}

function send_message($data) {
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

function count_users_by_login($login){
    $db = db_connect();

    if ($db == false){
        return 0;
    }

    $query = '
        SELECT COUNT(*) as `count`
        FROM `users`
        WHERE `login` = :login';

    $data = [':login' => $login];

    $result = $db->select($query, $data);
    $result->execute();

    return $result->fetch()['count'];
}

function count_users_by_email($email){
    $db = db_connect();

    if ($db == false){
        return 0;
    }

    $query = '
        SELECT COUNT(*) as `count`
        FROM `users`
        WHERE `email` = :email';

    $data = [':email' => $email];

    $result = $db->select($query, $data);
    $result->execute();

    return $result->fetch()['count'];
}

function set_user($login, $email, $password){
    $db = db_connect();

    if ($db == false){
        return false;
    }

    $query = '
        INSERT INTO `users`(`login`, `email`, `password`)
        VALUE (:login, :email, :password)';

    $data = [
        ':login' => $login,
        ':email' => $email,
        ':password' => $password
    ];

    return $db->execute($query, $data);
}

function db_connect(){
    $user_name = 'root';
    $password = '123';
    $db_name = 'test';

    try {
        $db = new PDO('mysql:host=localhost;dbname=' . $db_name, $user_name, $password);

        return $db;
    } catch (PDOException $exception) {
        echo '<br>Помилка підключення до бази данник<br>'.$exception->getMessage();

        return false;
    }
}