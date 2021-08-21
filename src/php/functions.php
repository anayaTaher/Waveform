<?php
function login($username, $password, $x){

    $db = new mysqli('localhost', 'root', '', 'waveform');

    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $strQry = "SELECT * FROM users WHERE username = '$username'";
    $res = $db->query($strQry);

    if ($res->num_rows == 0) {
        $db->close();
        return "Either username or password is incorrect!";
    }

    $row = $res->fetch_assoc();
    if ($username == $row['username'] && sha1($password) == $row['password']) {
        $_SESSION['isLoggedIn'] = $row['userid'];
        if ($x){
            // 86400 = 1 day
            setcookie("rememberMe", $row['userid'], time() + (86400 * 30));
        }
        header("Location: index.php");
    }
    else{
        $db->close();
        return "Either username or password is incorrect!";
    }

    $db->close();
}

function signUp($username, $password, $passwordConf, $email){
    $flag = 0;
    $error = 0;

    if($username == "" || $password == "" || $passwordConf == "" || $email == ""){
        return "All fields are required!";
    }

    $db = new mysqli('localhost', 'root', '', 'waveform');

    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }

    if (!$error) {
        $strQry = "SELECT * FROM users WHERE username = '$username'";
        $res = $db->query($strQry);

        if($res->num_rows != 0){
            return "Username is already in use!";
        }

        $shPassword = sha1($password);

        if ($flag == 0) {
            $strQry = "INSERT INTO `users` VALUES ('$username', '$email', '$shPassword', null, null, null, null, null, null, null, null, null);";
            $res = $db->query($strQry);
            $db->commit();
            $db->close();
            if ($res == true){
                $db = new mysqli('localhost', 'root', '', 'waveform');
                $strQry = "select * from users where username = '$username'";
                $res = $db->query($strQry);
                $row = $res->fetch_assoc();
                $_SESSION['isLoggedIn'] = $row['userid'];
                header("Location: index.php");
            }
            else
                return "Registration Failed";
        }
        $db->close();
    }
}