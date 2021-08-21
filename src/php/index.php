<?php

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
<div class="main">
    <table id="mainTable">
        <tr>
            <td style="width: 50%">
                <div class="mainContent">
                    <span id="welcome">Welcome to waveform!</span>
                    <div id="welcomeMsg">
                        <ul>
                            <li>Welcome to waveform, where you can find all the audible media you require for your
                                content as a content creator!<br><br></li>
                            <li>We provide everything you require from all generes including music, nature, electronic
                                sounds etc..<br><br></li>
                            <li>Everything we provide is completely free with an easy to use user interface, and special
                                features for our members.<br><br></li>
                            <li>We also allow our members to be able to share and upload their work without any
                                limitations.<br><br></li>
                            <li>You can get started buy clicking on the buttons below:<br><br></li>
                        </ul>
                    </div>
                    <?php
                    if ($_SESSION['isLoggedIn'] == -1) {
                        echo '
                    <ul class="mainButtonList">
                        <li>
                            <button onclick="openSignUpPage()" id="beginUsingGuest" type="button">Register Now!</button>
                        </li>
                        <li>
                            <a style="color:#E0E0E0;" href="search.php"><button id="beginUsing" type="button">Begin as a Guest!</button></a>
                        </li>
                    </ul>';
                    } else {
                        echo '
                    <ul class="mainButtonList">
                        <li style="width: 100%;">
                            <a style="color:#E0E0E0;" href="search.php"><button id="beginUsing" type="button" style="width: 80%">Begin Using Now!</button></a>
                        </li>
                    </ul>';
                    }
                    ?>
                </div>
            </td>
            <td style="width: 50%">
                <div class="mainContent">
                    <img src="../../images/homepage.png" alt="">
                </div>
            </td>
        </tr>
    </table>
</div>
<script>
    let modal = document.getElementById('myModal');
    let modalContent = document.getElementById('myModalContent');
    let modal2 = document.getElementById('myModal2');
    let modalContent2 = document.getElementById('myModalContent2');
    let pass = document.getElementById("password");

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