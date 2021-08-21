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

$consoleMsg = "";
$consoleType = 1;
$consolePackMsg = "";
$consolePackType = 1;

if(isset($_REQUEST['upload'])){
    if($_FILES['soundFile']['size'] == 0){
        $consoleMsg = "Please specify a file!";
        $consoleType = 0;
    }
    else{
        $db = new mysqli('localhost', 'root', '', 'waveform');
        if (mysqli_connect_errno()) {
            echo "Error: Could Not Connect To Database";
            die();
        }

        $trackName = $_REQUEST['trackName'];
        $id = $_SESSION['isLoggedIn'];
        $private = isset($_REQUEST['private']) && $_REQUEST['private'] == "Yes" ? 1 : 0;
        $packId = $_REQUEST['selectPack'];

        if($_REQUEST['trackName'] == ""){
            $consoleMsg = "Please specify a track name!";
            $consoleType = 0;
        }
        else {
            $stmt = "SELECT `trackName` FROM `tracks` WHERE `userUploaded` = $id AND `trackName` = '$trackName'";
            $res = $db->query($stmt);
            if ($res->num_rows != 0) {
                $consoleMsg = "A track with this name already exists!";
                $consoleType = 0;
            } else {
                $stmt = "SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'waveform' AND TABLE_NAME   = 'tracks';";
                $val =  $db->query($stmt);
                $vres = $val->fetch_assoc();
                $nextTrackId = $vres['AUTO_INCREMENT'];
                $dir = "../../uploads/" . $_SESSION['isLoggedIn'] . "/tracks/";
                $ext = pathinfo($_FILES['soundFile']['name'], PATHINFO_EXTENSION);
                if ($ext == "wav" || $ext == "mp3" || $ext == "flac"){
                    $file = $dir . $nextTrackId . "." . $ext;
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    move_uploaded_file($_FILES['soundFile']['tmp_name'], $file);
                    $consoleMsg = "The track was successfully uploaded!";
                    $stmt = "INSERT INTO `tracks` VALUES ('$trackName', '$file', $id, 0, 0, $private, ".($packId == "NO" ? "NULL" : $packId).");";
                    if (!$db->query($stmt)) {
                        echo $db->error;
                    };
                    $db->commit();
                }
                else{
                    $consoleMsg = "Your track has to be in .wav/.flac/.mp3 format!";
                    $consoleType = 0;
                }
            }
        }
        $db->close();
    }
}

if(isset($_REQUEST['pack'])){

    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }

    $id = $_SESSION['isLoggedIn'];
    $packName = $_REQUEST['packName'];

    if($packName == ""){
        $consolePackMsg = "Please specify a pack name!";
        $consolePackType = 0;
    }
    else {
        $stmt = "SELECT `packName` FROM `packs` WHERE `userOwns` = $id AND `packName` = '$packName'";
        $res = $db->query($stmt);
        if ($res->num_rows != 0) {
            $consoleMsg = "A pack with this name already exists!";
            $consolePackType = 0;
        } else {
            $stmt = "INSERT INTO `packs` VALUES (NULL , '$packName', $id);";
            if (!$db->query($stmt)) {
                echo $db->error;
            }
            $db->commit();
            $consolePackMsg = "Pack has been successfully created!";
            $consolePackType = 1;
        }
    }
    $db->close();
}
$db = new mysqli('localhost', 'root', '', 'waveform');
if (mysqli_connect_errno()) {
    echo "Error: Could Not Connect To Database";
    die();
}
$stmt = "SELECT * FROM `packs` WHERE `userOwns` = ".$_SESSION['isLoggedIn'];
$packsResult = $db->query($stmt);
$db->close();

include_once("Header.php");
include_once("TextField.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>waveform - Track Uploader</title>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="../../images/waveform.svg" rel="icon">
    <link href="../fontawesome/public/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/userFiles.css">
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
<form action="userFilesUpload.php" method="post" enctype="multipart/form-data">
<div class="filesContainer">
    <div class="filesLeftMenu">
        <div class="filesLeftMenuSelector">
            <a href="userFilesUpload.php">
                <div class="filesLeftMenuOption">
                    <div style="width: 70%">
                    <i class="fa fa-upload"></i> <span>New Track/Pack</span>
                    </div>
                </div>
            </a>
            <a href="userFilesManageTracks.php">
                <div class="filesLeftMenuOption">
                    <div style="width: 70%">
                        <i class="fa fa-file"></i> <span>Track Manager</span>
                    </div>
                </div>
            </a>
            <a href="userFilesManagePacks.php">
                <div class="filesLeftMenuOption">
                    <div style="width: 70%">
                        <i class="fa fa-book"></i> <span>Pack Manager</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="filesCenterMenu">
        <div class="filesCenterMenuTitle">
            <i class="fa fa-upload"></i> <span>New Track</span>
        </div>
            <div class="filesCenterMenuSubTitle">
                <i class="fa fa-plus"></i> <span>Choose the file to upload</span>
            </div>
            <div class="filesCenterMenuSection">
                <input type="file" name="soundFile" id="soundFile" accept=".mp3, .wav, .flac">
            </div>
            <div class="filesCenterMenuSubTitle">
                <i class="fa fa-info"></i> <span>Track Details</span>
            </div>
            <div class="filesCenterMenuSection" style="">
                <div class="filesCenterMenuTable">
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="trackName">Track Name:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <input type="text" name="trackName" id="trackName" placeholder="Track Name">
                        </div>
                    </div>
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="private">Private:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <input type="checkbox" name="private" id="private" value="Yes">
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="filesCenterMenuSubTitle">-->
<!--                <i class="fa fa-upload"></i> <span>Upload Track</span>-->
<!--            </div>-->
            <div class="filesCenterMenuSection">
                <input type="submit" name="upload" value="Upload">
            </div>
            <div class="filesCenterMenuSection">
                <span class="fileUploadConsole<?php echo ($consoleType == 1 ? "Green" : "Red")?>"><?php echo $consoleMsg; ?></span>
            </div>
            <div class="filesCenterMenuTitle">
                <i class="fa fa-book"></i> <span>New Pack</span>
            </div>
            <div class="filesCenterMenuSection">
                <div class="filesCenterMenuTable">
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="packName">Pack Name:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <input type="text" name="packName" id="packName" placeholder="Pack Name">
                        </div>
                    </div>
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <input id="packButton" type="submit" name="pack" value="Create Pack">
                        </div>
                    </div>
                </div>
                <div class="filesCenterMenuSection">
                    <span class="fileUploadConsole<?php echo ($consolePackType == 1 ? "Green" : "Red")?>"><?php echo $consolePackMsg; ?></span>
                </div>
            </div>
    </div>
    <div class="filesRightMenu" >
        <div class="filesCenterMenuTitle">
            <i class="fa fa-book"></i> <span>Available packs</span>
        </div>
            <div class="filesRightPacks">
                <div class="filesRightPacksCell" id="firstCell">
                    <div class="filesRightPacksCellLeft">
                        <span>Pack Name</span>
                    </div>
                    <div class="filesRightPacksCellRight">
                        <span>Select Pack</span>
                    </div>
                </div>
                <div class="filesRightPacksCell">
                    <div class="filesRightPacksCellLeft">
                        <span style="color: #e68282">NO PACK</span>
                    </div>
                    <div class="filesRightPacksCellRight">
                        <input type="radio" name="selectPack" value="NO" checked>
                    </div>
                </div>
                <?php
                for($i = 0 ; $i < $packsResult->num_rows ; $i++){
                    $row = $packsResult->fetch_assoc();
                    echo '
                        <div class="filesRightPacksCell">
                            <div class="filesRightPacksCellLeft">
                                <span>'.$row['packName'].'</span>
                            </div>
                            <div class="filesRightPacksCellRight">
                                <input type="radio" name="selectPack" value="'.$row['packid'].'">
                            </div>
                        </div>
                    ';
                }
                ?>
            </div>
    </div>
</div>
</form>
</body>

