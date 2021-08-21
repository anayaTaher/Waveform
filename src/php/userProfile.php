<?php

if (session_status() == PHP_SESSION_NONE)
    session_start();

if(isset($_COOKIE['rememberMe'])){
    $_SESSION['isLoggedIn'] = $_COOKIE['rememberMe'];
}

if (!isset($_SESSION['isLoggedIn'])){
    $_SESSION['isLoggedIn'] = -1;
}

if (!isset($_REQUEST['userid'])){
    header("Location: index.php");
}

$db = new mysqli('localhost', 'root', '', 'waveform');
if (mysqli_connect_errno()) {
    echo "Error: Could Not Connect To Database";
    die();
}

$strQry = 'select * from users where userid = ' . $_REQUEST['userid'] ;
$resP = $db->query($strQry);
if ($resP->num_rows == 0){
    header("Location: index.php");
}
$row = $resP->fetch_assoc();

$errorMsg = "";
$errorMsg2 = "";

require("functions.php");

if (isset($_REQUEST['username']) && isset($_REQUEST['password']))
    $errorMsg = login($_REQUEST['username'], $_REQUEST['password'], isset($_REQUEST['rememberMe']));

if (isset($_REQUEST['username']) && isset($_REQUEST['password']) && isset($_REQUEST['passwordConf']) && isset($_REQUEST['email']))
    $errorMsg2 = signUp($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['passwordConf'], $_REQUEST['email']);

include_once("Header.php");
include_once("TextField.php");
include_once("LoginClass.php");
include_once("SignUpClass.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>waveform - <?php echo $row['username']?>'s profile</title>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="../../images/waveform.svg" rel="icon">
    <link href="../fontawesome/public/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/signup.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/userProfile.css">
</head>
<body>
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
    $row2 = $res->fetch_assoc();
    $x->setUsername($row2['username']);
    if ($row2['profilePicture'] == null)
        $x->setProfilePicture('../../images/avatar.png');
    else
        $x->setProfilePicture($row2['profilePicture']);
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

<?php
include "userProfileClass.php";


$userId = -1;
$newProfile = new userProfile();
$newProfile->setUsername($row['username']);
$newProfile->setUserId($row['userid']);
$newProfile->setProfilePicture($row['profilePicture']);
$newProfile->setAbout($row['about']);
$newProfile->setRelease($row['releases']);
$newProfile->setMediaTwitter($row['twitter']);
$newProfile->setMediaPatreon($row['patreon']);
$newProfile->setMediaFacebook($row['facebook']);
$newProfile->setContactInformationGmail($row['gmail']);
$newProfile->setContactInformationDiscord($row['discord']);
$newProfile->display();

?>


<script>
    let modal = document.getElementById('myModal');
    let modalContent = document.getElementById('myModalContent');
    let modal2 = document.getElementById('myModal2');
    let modalContent2 = document.getElementById('myModalContent2');
    let about = document.getElementById('about');
    let release = document.getElementById('release');

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

    about.onclick = function myF1() {

        document.getElementById('aboutDiv').style.display = "block";
        document.getElementById('releaseDiv').style.display = "none";
        about.style.backgroundColor = "#5FACDD";
        release.style.backgroundColor = "#40485E"

    }

    release.onclick = function myF2() {

        document.getElementById('aboutDiv').style.display = "none";
        document.getElementById('releaseDiv').style.display = "flex";
        about.style.backgroundColor = "#40485E";
        release.style.backgroundColor = "#5FACDD"

    }

    window.onload = () => {
        document.getElementById('aboutDiv').style.display = "block";
        document.getElementById('releaseDiv').style.display = "none";
        about.style.backgroundColor = "#5FACDD";
        release.style.backgroundColor = "#40485E"
    };

</script>
</body>
</html>
