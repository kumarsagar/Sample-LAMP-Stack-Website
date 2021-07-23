<?php

function site_name()
{
    echo config('name');
}

function site_url()
{
    echo config('site_url');
}

function site_version()
{
    echo config('version');
}

function nav_menu($sep = ' | ')
{
    $nav_menu = '';
    $nav_items = config('nav_menu');
    
    foreach ($nav_items as $uri => $name) {
        $query_string = str_replace('page=', '', $_SERVER['QUERY_STRING'] ?? '');
        $class = $query_string == $uri ? ' active' : '';
        $url = config('site_url') . '/' . (config('pretty_uri') || $uri == '' ? '' : '?page=') . $uri;
        
        // Add nav item to list. See the dot in front of equal sign (.=)
        $nav_menu .= '<a href="' . $url . '" title="' . $name . '" class="item ' . $class . '">' . $name . '</a>' . $sep;
    }

    echo trim($nav_menu, $sep);
}

function page_title()
{
    $page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'Charlie';

    echo ucwords(str_replace('-', ' ', $page));
}

function page_content()
{
    $queryParameters = $_SERVER['QUERY_STRING'];
    $name = explode("=", $queryParameters)[0];
    $value = explode("=", $queryParameters)[1];
    $page="";
    if($name == "page"){
        $page = $value;
        if($page == "Charlie")
            echo (isBitten() ? "<p>Charlie bit your finger!</p>" : "<p>Charlie did not bite your finger!</p>");
        else if($page == "countWords")
            echo '<form  action="" method="GET">
                    <input type="text" name="str">
                    <input type="submit" value="Submit">
                </form>';
        else if($page == "SignUp"){
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="password" name="password">
                    <input required type="tel" name="phone">
                    <input type="submit" value="Register">
                </form>';
        }
        else if($page == "SignIn"){
            if($_SESSION['LoggedIn'] == true){
                echo 'You are already Logged In!<form action="" method="POST"><input type="hidden" value="LogOut" name="LogOut"><button type="submit">Log Out</button></form>';

            }else{
                echo '<form  action="" method="POST">
                        <input  required type="text" name="username">
                        <input required type="password" name="password">
                        <input type="checkbox" value="1" name="isRememberMeSet">Remember me</input>
                        <input type="submit" value="Submit">
                     </form>';
            }
        }
        else if($page == "ResetPassword"){
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="phone" name="phone">
                    <input type="submit" value="Submit">
                </form>';
        }
        else if($page == "ChangePassword"){
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="password" name="oldPassword">
                    <input required type="password" name="newPassword">
                    <input type="submit" value="Submit">
                </form>';
        }
        else if($page == "LogOut"){
                echo 'You are Logged Out! <form  action="" method="POST">
                        <input  required type="text" name="username">
                        <input required type="password" name="password">
                        <input type="checkbox" value="1" name="isRememberMeSet">Remember me</input>
                        <input type="submit" value="Submit">
                     </form>';
        }
        else 
            echo "<p> 404 Error : Page does not exist!</p>";
    }
    else if($name == 'str'){
        $countsOfWords = countWords($value);
        $tableHtmlString = "<table><tr><th>Word</th><th>Count</th></tr>";
        foreach($countsOfWords as $word=>$count){
            $tableHtmlString .= "<tr><td>".$word."</td><td>".$count."</td></tr>";
        }
        $tableHtmlString .= "</table>";
        echo $tableHtmlString;
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['phone'])){
        try {
            $sql = "INSERT INTO 6470exerciseusers (USERNAME, PASSWORD_HASH, PHONE)
                                                    VALUES (".trim($_POST['username']).", ".sha1(trim($_POST['password']), false).", ".trim($_POST['phone']).")";
            $conn->exec($sql);
            echo "Registration is successful";
          } catch(PDOException $e) {
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="password" name="password">
                    <input required type="tel" name="phone">
                    <input type="submit" value="Register">
                </form></br>'.$sql . "<br>" . $e->getMessage();
          }
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && isset($_POST['password']) && !isset($_POST['phone'])){
        try {
            $sql = "SELECT * FROM 6470exerciseusers WHERE USERNAME=".$_POST['username']." AND PASSWORD_HASH = ".sha1($_POST['password'], false);
            $conn->exec($sql);
            $_SESSION['LoggedIn'] = true;
            if($_POST['isRememberMeSet'] == 1){
                setcookie('user', $_POST['username'], time()+1*60*60, $_SERVER['PATH_TRANSLATED'], $_SERVER['REMOTE_ADDR'], true, true);
            }
            echo "Welcome ".$_POST['username']." !";
          } catch(PDOException $e) {
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="password" name="password">
                    <input type="submit" value="Submit">
                </form></br>'.$sql . "<br>" . $e->getMessage();
          }
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && !isset($_POST['password']) && isset($_POST['phone'])){
        try {
            $sql = "SELECT * FROM 6470exerciseusers WHERE USERNAME=".$_POST['username']." AND PHONE = ".$_POST['phone'].")";
            $user = $conn->query($sql);
            if($user==null)
                echo 'You have not registered yet!<form  action="" method="POST">
                        <input  required type="text" name="username">
                        <input required type="phone" name="phone">
                        <input type="submit" value="Submit">
                    </form></br>';
            else{
                $newPassword = generateRandomString();
                $sql = "INSERT INTO 6470exerciseusers (USERNAME, PASSWORD_HASH, PHONE)
                                                    VALUES (".trim($_POST['username']).", ".sha1($newPassword, false).", ".trim($_POST['phone']).")";
                $conn->exec($sql);
                echo "Your new password is : ".$newPassword;
            }
          } catch(PDOException $e) {
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="phone" name="phone">
                    <input type="submit" value="Submit">
                </form></br>'.$sql . "<br>" . $e->getMessage();
          }
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && !isset($_POST['password']) && !isset($_POST['phone']) && isset($_post['oldPassword']) && isset($_post['newPassword']) ){
        try {
            $sql = "SELECT * FROM 6470exerciseusers WHERE USERNAME=".$_POST['username']." AND PASSWORD_HASH = ".sha1($_POST['oldPassword'], false).")";
            $user = $conn->query($sql);
            if($user==null)
                echo 'You have entered wrong credentials!<form  action="" method="POST">
                        <input  required type="text" name="username">
                        <input required type="password" name="oldPassword">
                        <input required type="password" name="newPassword">
                        <input type="submit" value="Submit">
                    </form>';
            else{
                $newPassword = generateRandomString();
                $sql = "INSERT INTO 6470exerciseusers (USERNAME, PASSWORD_HASH, PHONE)
                                                    VALUES (".trim($_POST['username']).", ".sha1($newPassword, false).", ".trim($_POST['phone']).")";
                $conn->exec($sql);
                echo "Your new password is : ".$newPassword;
            }
          } catch(PDOException $e) {
            echo '<form  action="" method="POST">
                    <input  required type="text" name="username">
                    <input required type="password" name="oldPassword">
                    <input required type="password" name="newPassword">
                    <input type="submit" value="Submit">
                </form></br>'.$sql . "<br>" . $e->getMessage();
          }
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['LogOut']) ){
        $_SESSION['LoggedIn'] = false;
        setcookie("user",$_COOKIE['user'], time() - 3600);
        echo 'You are Logged Out!<form  action="" method="POST">
                <input  required type="text" name="username">
                <input required type="password" name="password">
                <input type="submit" value="Submit">
            </form>';
    }
    
}

function init()
{
    require config('template_path') . '/template.php';
}

 
function isBitten(){
    return (rand(0,1) == 0 ? false : true);
}

function countWords($str){
    $counts = array();
    $words = explode("+", strtolower($str));
    foreach($words as $word){
        if(isset($counts[$word]))
            $counts[$word] = $counts[$word] + 1;
        else
            $counts[$word] = 1;
    }
    arsort($counts);
    return $counts;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}