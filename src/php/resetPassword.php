<?php

$myMsg = "";

if (session_status() == PHP_SESSION_NONE)
    session_start();

if (isset($_COOKIE['rememberMe']))
    $_SESSION['isLoggedIn'] = $_COOKIE['rememberMe'];

if (!isset($_SESSION['isLoggedIn']))
    $_SESSION['isLoggedIn'] = -1;

$errorMsg = "";
$errorMsg2 = "";

require("functions.php");

if (isset($_REQUEST['loginUsername']) && isset($_REQUEST['loginPassword']))
    $errorMsg = login($_REQUEST['loginUsername'], $_REQUEST['loginPassword'], isset($_REQUEST['rememberMe']));

if (isset($_REQUEST['signUpUsername']) && isset($_REQUEST['signUpPassword']) && isset($_REQUEST['signUpPasswordConf']) && isset($_REQUEST['signUpEmail']))
    $errorMsg2 = signUp($_REQUEST['signUpUsername'], $_REQUEST['signUpPassword'], $_REQUEST['signUpPasswordConf'], $_REQUEST['signUpEmail']);

include("configReset.php");

if (!isset($_REQUEST['code']))
    header("Location: index.php");

$code = $_REQUEST['code'];

$strQry = "SELECT email FROM resetpasswords WHERE code = '$code'";
$res = $db->query($strQry);
if ($res->num_rows == 0) {
    header("Location: index.php");
}
$row = $res->fetch_assoc();
$myEmail = $row["email"];

if (isset($_REQUEST["password"])) {

    $pass = sha1($_REQUEST["password"]);


    $strQry = "UPDATE users SET password = '$pass' where email = '$myEmail'";
    $res = $db->query($strQry);

    if ($res) {
        $strQry = "DELETE FROM resetpasswords where code = " . $code;
        $res = $db->query($strQry);
        $myMsg = "<span style='color:#82E69E;'>Password Updated</span>";
    } else {
        $myMsg = "<span style='color:#E68282;'>Something Went Wrong</span>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../../images/waveform.svg" rel="icon">
    <link href="../fontawesome/public/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/signup.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/contactUs.css">
    <style>
        #resetPasswordButton {
            border: none;
            background-color: #479eca;
            width: 100%;
            height: 30px;
            color: #e0e0e0;
            font-family: "Century Gothic", serif;
            font-size: 10pt;
            border-radius: 5px;
        }
    </style>
</head>
<body <?php if ($errorMsg != null) echo 'onload="openLoginPage()"';
if ($errorMsg2 != null) echo 'onload="openSignUpPage()"' ?>>

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

<form method="post">
    <div class="mainContainer" style="height: 60%; top: 50px; width: 25%">
        <div style="margin-top: 40px">
            <img src="../../images/waveform.svg" height="150" width="150" alt="" style="align-self: center">
        </div>
        <span class="title" style="font-size: 14pt; margin-top: 10pt">Please enter your new password:</span>
        <div class="mainContainerTable">
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index2">
                    <input type="password" id="resetPassword" name="password" placeholder="New Password">
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index2">
                    <input type="password" id="confPassword" name="confPassword" placeholder="Confirm Password">
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index2">
                    <button type="button" name="submit" id="resetPasswordButton" style="background-color:#479ECA;">
                        Change password
                    </button>
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index2">
                    <button type="reset" name="submit" id="sendMessage" style="background-color:#E68282;">Clear</button>
                </div>
            </div>
        </div>
        <span class="console" id="resetConsole"> <?php echo $myMsg ?> </span>
    </div>
</form>
<script>
    let modal = document.getElementById('myModal');
    let modalContent = document.getElementById('myModalContent');
    let modal2 = document.getElementById('myModal2');
    let modalContent2 = document.getElementById('myModalContent2');
    let pass = document.getElementById("password");
    let username = document.getElementById("signUpUsername");
    let password = document.getElementById("signUpPassword");
    let passwordConf = document.getElementById("signUpPasswordConf");
    let email = document.getElementById("signUpEmail");
    let myP = document.getElementById("console2");
    let submit = document.getElementById('signUpButton');
    let usernamePattern = /^(?=.{6,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/;
    let passwordPattern = /^\w{8,30}$/;
    let emailPattern = /^([\w_\-.])+@([\w_\-.]+)\.([A-z]{2,5})$/;


    let resetPassword = document.getElementById("resetPassword");
    let confPassword = document.getElementById("confPassword");
    let resetPasswordButton = document.getElementById('resetPasswordButton');
    let resetConsole = document.getElementById("resetConsole")

    function openLoginPage() {
        modal.style.display = "block";
    }

    function openSignUpPage() {
        modal2.style.display = "block";
    }

    resetPasswordButton.onclick = () => {

        if (resetPassword.value.length < 8)
            resetConsole.innerHTML = "Password must be at least 8 characters.";
        else if (resetPassword.value !== confPassword.value)
            resetConsole.innerHTML = "Password Mismatch";
        else
            resetPasswordButton.type = "submit";

    }

    window.onclick = function (event) {
        if (event.target === modalContent) {
            modal.style.display = "none";
        }
        if (event.target === modalContent2) {
            modal2.style.display = "none";
        }
    }

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
