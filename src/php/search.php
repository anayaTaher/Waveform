<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_COOKIE['rememberMe'])) {
    $_SESSION['isLoggedIn'] = $_COOKIE['rememberMe'];
}

if (!isset($_SESSION['isLoggedIn'])) {
    $_SESSION['isLoggedIn'] = -1;
}

$errorMsg = "";
$errorMsg2 = "";
$searchResult = [];
$searchResultSize = 0;

require("functions.php");

if (isset($_REQUEST['loginUsername']) && isset($_REQUEST['loginPassword'])) {
    $errorMsg = login($_REQUEST['loginUsername'], $_REQUEST['loginPassword'], isset($_REQUEST['rememberMe']));
}

if (isset($_REQUEST['signUpUsername']) && isset($_REQUEST['signUpPassword']) && isset($_REQUEST['signUpPasswordConf']) && isset($_REQUEST['signUpEmail'])) {
    $errorMsg2 = signUp($_REQUEST['signUpUsername'], $_REQUEST['signUpPassword'], $_REQUEST['signUpPasswordConf'], $_REQUEST['signUpEmail']);
}

if (isset($_REQUEST['search']) && $_REQUEST['searchField'] != ""){
    $search = $_REQUEST['searchField'];
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "
        SELECT `trackName`, `trackid`, `trackDir`, `userUploaded`, `packContaining` FROM `tracks` WHERE `trackName` LIKE '%$search%' AND `private` = 0 
        UNION 
        SELECT `trackName`, `trackid`, `trackdir`, `userUploaded`, `packContaining` FROM `tracks` INNER JOIN `packs` ON `packContaining` = `packid` WHERE (`trackName` LIKE '%$search%' OR `packName` LIKE '%$search%' ) AND `private` = 0
        UNION
        SELECT `trackName`, `trackid`, `trackdir`, `userUploaded`, `packContaining` FROM `tracks` INNER JOIN `users` ON `userUploaded` = `userid` WHERE `username` LIKE '%$search%';
        ";
    $searchResult = $db->query($stmt);
    if ($searchResult == true){
        $searchResultSize = $searchResult->num_rows;
    }
    else{
        echo $db->error;
    }
    $db->close();
}

include_once("Header.php");
include_once("TextField.php");
include_once("LoginClass.php");
include_once("SignUpClass.php");
include_once("WaveformSound.php");

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
    <link rel="stylesheet" href="../css/search.css">
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
<div class="searchHeader">
    <form action="search.php" method="post">
        <div class="searchContainer">
            <label><i class="fa fa-search"></i><span>Search:</span></label>
            <input type="text" name="searchField" id="searchField" placeholder="Search">
            <input type="submit" name="search" value="Search">
        </div>
    </form>
</div>
<div class="searchResult">
    <div class="numberOfResults"><?php echo $searchResultSize . " Result".($searchResultSize != 1 ? "s" : "")." found" ?></div>
    <?php
    for($i = 0 ; $i < $searchResultSize ; $i++){
        $row = $searchResult->fetch_assoc();
        $x = new WaveformSound();
        $x->setId($row['trackid']);
        $x->setUserId($row['userUploaded']);
        $x->setTrackName($row['trackName']);
        $x->setTrackAudio($row['trackDir']);
        if ($row['packContaining'] != NULL){
            $x->setPackId($row['packContaining']);
        }
        $x->display();
    }
    ?>
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