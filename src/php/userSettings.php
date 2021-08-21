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

if ($_SESSION['isLoggedIn'] == -1) {
    header("Location: index.php");
}

$errorMsg = "";

if (isset($_REQUEST['settingsSaveChanges'])) {

    $v1 = $_REQUEST['settingsUsername'];
    $v2 = $_REQUEST['settingsEmail'];
    $v3 = sha1($_REQUEST['settingsOldPassword']);
    $v4 = sha1($_REQUEST['settingsNewPassword']);
    $v5 = $_REQUEST['settingsTwitter'];
    $v6 = $_REQUEST['settingsPatreon'];
    $v7 = $_REQUEST['settingsFacebook'];
    $v8 = $_REQUEST['settingsGmail'];
    $v9 = $_REQUEST['settingsDiscord'];
    $v10 = $_REQUEST['aboutTextArea'];
    $patternEmail = "/^([\w_\-.])+@([\w_\-.]+)\.([A-z]{2,5})$/";
    $patternUsername = "/^(?=.{6,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/";
    $patternPassword = "/^\w{8,30}$/";

    $errorFromImage = 0;

    $db = new mysqli('localhost', 'root', '', 'waveform');

    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }

    $strQry = 'select * from users';
    $res = $db->query($strQry);

    $oldPassword = 0;

    for ($i = 0; $i < $res->num_rows; $i++) {
        $row = $res->fetch_assoc();
        if ($_SESSION['isLoggedIn'] == $row['userid']) {
            $oldPassword = $row['password'];
        }
    }

    $print = true;


    $strQry = "UPDATE users SET twitter = '$v5', patreon = '$v6', facebook = '$v7', gmail = '$v8', discord='$v9', about = '$v10' where userid = " . $_SESSION['isLoggedIn'];
    $res = $db->query($strQry);
    if (isset($_REQUEST['deleteProfilePicture'])){
        $stmt = "UPDATE `users` SET `profilePicture` = NULL WHERE `userid` = '". $_SESSION['isLoggedIn'] ."';";
        $db->query($stmt);
        $db->commit();
    }
    else if ($_FILES['profilePictureUploader']['size'] != 0) {
        $dir = "../../uploads/" . $_SESSION['isLoggedIn'] . "/profile/";
        $ext = pathinfo($_FILES['profilePictureUploader']['name'], PATHINFO_EXTENSION);
        if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "bmp" || $ext == "gif"){
            $file = $dir . "profilePicture" . "." . $ext;
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            move_uploaded_file($_FILES['profilePictureUploader']['tmp_name'], $file);
            $stmt = "UPDATE `users` SET `profilePicture` = '$file' WHERE `userid` = '". $_SESSION['isLoggedIn'] ."';";
            if (!$db->query($stmt)) {
                echo $db->error;
            };
            $db->commit();
        }
        else{
            $errorFromImage = 1;
        }
    }

    $strQry = 'select * from users where userid = ' . $_SESSION['isLoggedIn'];
    $res = $db->query($strQry);
    $row = $res->fetch_assoc();

    if ($errorFromImage){
        $errorMsg = "The image has to be in .jpg/.jpeg/.png/.bmp/.gif format!";
    }
    else{
        $errorMsg = "<span style='color: #82e69e;'>Changes saved</span>";
    }

    if (strlen($v1) >= 6) {
        if (preg_match($patternUsername, $v1)) {
            $strQry = "SELECT * FROM users WHERE username = '$v1'";
            $res = $db->query($strQry);

            if ($v1 != $row['username']) {
                if ($res->num_rows == 0) {
                    $strQry = "UPDATE users SET username = '$v1' where userid = " . $_SESSION['isLoggedIn'];
                    $res = $db->query($strQry);
                } else {
                    $errorMsg = "Username is already in use!";
                }
            }
        } else
            $errorMsg = "Username must only contain alphanumeric, underscore and periods.";

    } else
        $errorMsg = "Username must be at least 6 characters";

    if (preg_match($patternEmail, $v2)) {
        $strQry = "UPDATE users SET email = '$v2' where userid = " . $_SESSION['isLoggedIn'];
        $res = $db->query($strQry);
    } else
        $errorMsg = "Please enter a valid email address";

    if (strlen($_REQUEST['settingsNewPassword']) != 0) {

        if ($oldPassword == $v3) {
            if (strlen($_REQUEST['settingsNewPassword']) >= 8) {
                $strQry = "UPDATE users SET password = '$v4' where userid = " . $_SESSION['isLoggedIn'];
                $res = $db->query($strQry);
            } else
                $errorMsg = "Password must be at least 8 characters";
        } else {

            $errorMsg = "Incorrect Old Password";

        }
    }

    $db->commit();
    $db->close();

}
if (isset($_REQUEST['rollBack'])) {
    header("Refresh:0"); // Refresh Page
}

$db = new mysqli('localhost', 'root', '', 'waveform');
$strQry = 'select * from users where userid = ' . $_SESSION['isLoggedIn'];
$res = $db->query($strQry);
$row = $res->fetch_assoc();
$username = $row['username'];
$email = $row['email'];
$about = $row['about'];
$releases = $row['releases'];
$twitter = $row['twitter'];
$patreon = $row['patreon'];
$facebook = $row['facebook'];
$gmail = $row['gmail'];
$discord = $row['discord'];
if ($row['profilePicture'] == null)
    $profilePicture = "https://i.imgur.com/KuU6dNy.png";
else
    $profilePicture = $row['profilePicture'];

include_once("Header.php");
include_once("TextField.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Waveform - Settings</title>
    <link href="../../images/waveform.svg" rel="icon">
    <link href="../fontawesome/public/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/userSettings.css">
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
    $row = $res->fetch_assoc();
    $x->setUsername($row['username']);
    $x->setProfilePicture($row['profilePicture']);
}
$x->display();
?>
<form action="userSettings.php" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td class="userSettings">
                <div class="userSettingsOuter">
                    <div class="settingsMainCategory">
                        <i class="fa fa-gear"></i>
                        <span>User Settings</span>
                    </div>
                    <div class="userSettingsInner">
                        <div class="settingsSmallCategory">
                            <i class="fa fa-image"></i>
                            <span>Profile Picture</span>
                        </div>
                        <input type="file" id="profilePictureUploader" name="profilePictureUploader"
                               accept="image/jpeg, image/png">
                        <div class="deleteProfilePictureDiv">
                            <input type="checkbox" id="deleteProfilePicture" name="deleteProfilePicture">
                            <label for="deleteProfilePicture" id="deleteProfilePictureLabel">Delete Profile
                                Picture?</label>
                        </div>
                        <div class="settingsTextFields">
                            <div class="settingsSmallCategory">
                                <i class="fa fa-user"></i>
                                <span>User Information</span>
                            </div>
                            <?php
                            $x = new TextField();
                            $x->setPrompt("Username");
                            $x->setName("settingsUsername");
                            $x->showPrompt(true);
                            $x->setValue($username);
                            $x->display();
                            $x = new TextField();
                            $x->setPrompt("Email");
                            $x->setName("settingsEmail");
                            $x->showPrompt(true);
                            $x->setValue($email);
                            $x->display();
                            $x = new TextField();
                            $x->setPrompt("Old Password");
                            $x->setName("settingsOldPassword");
                            $x->showPrompt(true);
                            $x->isPassword(true);
                            $x->display();
                            $x = new TextField();
                            $x->setPrompt("New Password");
                            $x->setName("settingsNewPassword");
                            $x->showPrompt(true);
                            $x->isPassword(true);
                            $x->display();
                            ?>
                            <div class="settingsSmallCategory" style="margin-top: 30px">
                                <i class="fa fa-link"></i>
                                <span>Media</span>
                            </div>
                            <?php
                            $x = new TextField();
                            $x->setPrompt("Twitter");
                            $x->setName("settingsTwitter");
                            $x->showPrompt(true);
                            $x->setValue($twitter);
                            $x->display();
                            $x = new TextField();
                            $x->setPrompt("Patreon");
                            $x->setName("settingsPatreon");
                            $x->showPrompt(true);
                            $x->setValue($patreon);
                            $x->display();
                            $x = new TextField();
                            $x->setPrompt("Facebook");
                            $x->setName("settingsFacebook");
                            $x->showPrompt(true);
                            $x->setValue($facebook);
                            $x->display();
                            ?>
                            <div class="settingsSmallCategory" style="margin-top: 30px">
                                <i class="fa fa-phone"></i>
                                <span>Contact Information</span>
                            </div>
                            <?php
                            $x = new TextField();
                            $x->setPrompt("Gmail");
                            $x->setName("settingsGmail");
                            $x->showPrompt(true);
                            $x->setValue($gmail);
                            $x->display();
                            $x = new TextField();
                            $x->setPrompt("Discord");
                            $x->setName("settingsDiscord");
                            $x->showPrompt(true);
                            $x->setValue($discord);
                            $x->display();
                            ?>
                        </div>
                    </div>
                </div>
            </td>
            <td class="userAbout">
                <div class="aboutOuter">
                    <div class="settingsMainCategory">
                        <i class="fa fa-pencil"></i>
                        <span>About</span>
                    </div>
                    <label for="aboutTextArea" style="display: none"></label>
                    <textarea id="aboutTextArea" name="aboutTextArea" cols="100"
                              rows="33"><?php echo $about ?></textarea>
                </div>
            </td>
            <td class="userPreview">
                <div class="userPreviewOuter">
                    <div class="previewPicture">
                        <img id="previewPictureImage" src="<?php echo $profilePicture ?>" alt="">
                        <span id="previewPictureName"><?php echo $username ?></span>
                    </div>
                    <div class="previewButtons">
                        <div id="settingsConsole" style="font-size: 15px;">
                            <?php echo $errorMsg ?>
                        </div>
                        <div id="previewText">
                            You don’t need to type your password unless you are changing your user information
                        </div>
                        <button type="submit" name="settingsSaveChanges" id="settingsSaveChanges">Save Changes</button>
                        <button type="submit" name="rollBack" id="settingsRollBack">Roll Back</button>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</form>
</body>
</html>
