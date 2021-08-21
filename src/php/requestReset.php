<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_COOKIE['rememberMe'])) {
    $_SESSION['isLoggedIn'] = $_COOKIE['rememberMe'];
}

if (!isset($_SESSION['isLoggedIn'])) {
    $_SESSION['isLoggedIn'] = -1;
}

if ($_SESSION['isLoggedIn'] != -1) {
    header("Location: index.php");
}

$errorMsg = "";
$errorMsg2 = "";

$consoleMsg = "";
$consoleType = 0;

require("functions.php");

if (isset($_REQUEST['loginUsername']) && isset($_REQUEST['loginPassword'])) {
    $errorMsg = login($_REQUEST['loginUsername'], $_REQUEST['loginPassword'], isset($_REQUEST['rememberMe']));
}

if (isset($_REQUEST['signUpUsername']) && isset($_REQUEST['signUpPassword']) && isset($_REQUEST['signUpPasswordConf']) && isset($_REQUEST['signUpEmail'])) {
    $errorMsg2 = signUp($_REQUEST['signUpUsername'], $_REQUEST['signUpPassword'], $_REQUEST['signUpPasswordConf'], $_REQUEST['signUpEmail']);
}

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'configReset.php';

if (isset($_REQUEST['resetEmail'])) {
    $mail = new PHPMailer(true);
    $emailTo = $_REQUEST['resetEmail'];
    $code = uniqid(true);

    $strQry = "INSERT INTO `resetpasswords` VALUES (null, '$code', '$emailTo');";
    $res = $db->query($strQry);

    if ($res == false)
        exit("Error");

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'anaya.taher@gmail.com';
        $mail->Password = 'Taher7412369850010';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = '465';

        //Recipients
        $mail->setFrom('anaya.taher@gmail.com', 'Waveform');
        $mail->addAddress($emailTo);
        $mail->addReplyTo('no-reply@gmail.com', 'No Reply');

        // Content
        $url = "http://localhost:81/MyWebProjects/waveform/src/php/resetPassword.php?code=$code";
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'waveform - Your Password Reset Link';
        $mail->Body = "<h1>You Have Requested A Password Reset</h1> Click on <a href='$url'>this link</a> to reset your password";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        $consoleMsg = "A password reset link has been sent to your email";
        $consoleType = 0;
    } catch (Exception $e) {
        echo $e;
        $consoleMsg = "Invalid email address";
        $consoleType = 1;
    }
}

include_once("Header.php");
include_once("TextField.php");
include_once("LoginClass.php");
include_once("SignUpClass.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>waveform</title>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="../../images/waveform.svg" rel="icon">
    <link href="../fontawesome/public/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/signup.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/contactUs.css">
</head>
<body <?php if ($errorMsg != null) echo 'onload="openLoginPage()"';if ($errorMsg2 != null) echo 'onload="openSignUpPage()"'?>>

<?php
$userId = -1;
$x = new Header();
if ($_SESSION['isLoggedIn'] == -1) {
    $x->isLoggedIn(false);
} else {
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $x->isLoggedIn(true);
    $strQry = 'select * from users where userid = ' . $_SESSION['isLoggedIn'];
    $res = $db->query($strQry);
    $row = $res->fetch_assoc();
    $x->setUsername($row['username']);
    $x->setProfilePicture($row['profilePicture']);
}
$x->display();
?>
<div id="myModal" class="modal">
    <div id="myModalContent" class="modal-content">
        <?php
        $y = new Login();
        $y->setErrorMsg($errorMsg);
        $y->display();
        ?>
    </div>
</div>
<div id="myModal2" class="modal">
    <div id="myModalContent2" class="modal-content">
        <?php
        $z = new SignUp();
        $z->setErrorMsg($errorMsg2);
        $z->display();
        ?>
    </div>
</div>

<form action="requestReset.php" method="post" id="myForm">
    <div class="mainContainer" style="height: 60%; top: 50px; width: 25%">
        <div style="margin-top: 40px">
            <img src="../../images/waveform.svg" height="150" width="150" alt="">
        </div>
        <span class="title" style="font-size: 14pt">Please enter your email:</span>
        <div class="mainContainerTable">
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index2">
                    <input type="text" id="email" name="resetEmail" placeholder="Your Email">
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index2">
                    <button type="submit" name="submit" id="sendMessage">Send</button>
                </div>
            </div>
        </div>
        <span class="console" <?php echo ($consoleType == 0 ? 'style="color: #82e69e"' : ""); ?>><?php echo $consoleMsg ?></span>
    </div>
</form>

<script>
    let modal = document.getElementById('myModal');
    let modalContent = document.getElementById('myModalContent');
    let modal2 = document.getElementById('myModal2');
    let modalContent2 = document.getElementById('myModalContent2');
    let pass = document.getElementById('password");

    function openLoginPage() {
        modal.style.display = "block";
    }

    function openSignUpPage() {
        modal2.style.display = "block";
    }

    window.onclick = function (event) {
        if (event.target === modalContent) {
            modal.style.display = "none";
        }
        if (event.target === modalContent2) {
            modal2.style.display = "none";
        }
    }

    let username = document.getElementById("signUpUsername");
    let password = document.getElementById("signUpPassword");
    let passwordConf = document.getElementById("signUpPasswordConf");
    let email = document.getElementById("signUpEmail");
    let myP = document.getElementById("console2");
    let submit = document.getElementById('signUpButton');
    let usernamePattern = /^(?=.{6,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/;
    let passwordPattern = /^\w{8,30}$/;
    let emailPattern = /^([\w_\-.])+@([\w_\-.]+)\.([A-z]{2,5})$/;

    username.oninput = () => {
        if (usernamePattern.test(username.value))
            username.style.border = "#0F0 2px solid";
        else
            username.style.border = "#F00 2px solid";
    }

    password.oninput = () => {
        if (passwordPattern.test(password.value))
            password.style.border = "#0F0 2px solid";
        else
            password.style.border = "#F00 2px solid";
    }

    email.oninput = () => {
        if (emailPattern.test(email.value))
            email.style.border = "#0F0 2px solid";
        else
            email.style.border = "#F00 2px solid";
    }

    username.onblur = () => {
        if (usernamePattern.test(username.value))
            username.style.border = "#000 1px solid";
    }

    password.onblur = () => {
        if (passwordPattern.test(password.value))
            password.style.border = "#000 1px solid";
    }

    email.onblur = () => {
        if (emailPattern.test(email.value))
            email.style.border = "#000 1px solid";
    }

    submit.onclick = () => {

        if (usernamePattern.test(username.value) && passwordPattern.test(password.value) && emailPattern.test(email.value) && passwordConf.value === password.value)
            submit.type = "submit";

        else if (!usernamePattern.test(username.value)) {
            if (username.value.length < 6)
                myP.innerHTML = "Username must be at least 6 characters.";
            else
                myP.innerHTML = "Username must only contain alphanumeric";

        } else if (!emailPattern.test(email.value)) {
            myP.innerHTML = "Please enter a valid email address";

        } else if (!passwordPattern.test(password.value)) {
            if (password.value.length < 8)
                myP.innerHTML = "Password must be at least 8 characters.";
        } else if (passwordConf.value !== password.value) {

            myP.innerHTML = "Password Mismatch";

        }
    }
</script>
</body>
</html>
