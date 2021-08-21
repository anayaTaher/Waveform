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
include_once("Header.php");
include_once("TextField.php");
include_once("LoginClass.php");
include_once("SignUpClass.php");
include_once 'sendEmail.php';

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

<form action="contactUs.php" method="post" id="myForm">
    <div class="mainContainer">
        <div style="margin-top: 40px">
            <img src="../../images/waveform.svg" height="150" width="150" alt="">
        </div>
        <span class="title">Please fill out the following form:</span>
        <div class="mainContainerTable">
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index1">
                    <label for="name">Your Name:</label>
                </div>
                <div class="mainContainerTableCell index2">
                    <input type="text" id="name" name="name" placeholder="Your Name">
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index1">
                    <label for="email">Your Email:</label>
                </div>
                <div class="mainContainerTableCell index2">
                    <input type="text" id="email" name="email" placeholder="Your Email">
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index1">
                    <label for="subject">Mail Subject:</label>
                </div>
                <div class="mainContainerTableCell index2">
                    <input type="text" id="subject" name="subject" placeholder="Subject">
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index1">
                    <label for="message">Message:</label>
                </div>
                <div class="mainContainerTableCell index2">
                    <textarea id="message" placeholder="Type Your Message"></textarea>
                </div>
            </div>
            <div class="mainContainerTableRow">
                <div class="mainContainerTableCell index1">
                </div>
                <div class="mainContainerTableCell index2">
                    <button type="button" onclick="sendEmail()" id="sendMessage">Send</button>
                </div>
            </div>
        </div>
        <span id="sent-notification" class="console"></span>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>

    let name = document.getElementById("name");
    let emailC = document.getElementById("email");
    let subject = document.getElementById("subject");
    let message = document.getElementById("message");
    let myForm = document.getElementById("myForm");
    let resultMessage = document.getElementById("sent-notification");
    let emailPatternC = /^([\w_\-.])+@([\w_\-.]+)\.([A-z]{2,5})$/;

    function sendEmail() {
        if (name.value.length !== 0 && emailC.length !== 0 && subject.value.length !== 0 && message.value.length !== 0) {
            if (emailPatternC.test(emailC.value)) {
                $.ajax({
                    url: 'sendEmail.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        name: name.value,
                        email: emailC.value,
                        subject: subject.value,
                        message: message.value,
                    }, success: function () {
                        myForm.reset();
                        resultMessage.innerHTML = "<span style='color: #82e69e;'>Message was sent successfully!</span>";
                    }

                });
            }else {
                resultMessage.innerHTML = "<span style='color: #e68282;'>Invalid email!</span>";
            }
        } else {
            resultMessage.innerHTML = "<span style='color: #e68282;'>All fields are required!</span>";
        }
    }

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