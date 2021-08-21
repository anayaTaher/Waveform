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
$consoleMsgType = 0;
$packName = "Pack Name";

if(isset($_REQUEST['packEditSave'])){
    if (isset($_SESSION['packEditId'])){
        $db = new mysqli('localhost', 'root', '', 'waveform');
        if (mysqli_connect_errno()) {
            echo "Error: Could Not Connect To Database";
            die();
        }
        $stmt = "UPDATE `packs` SET `packName` = '". $_REQUEST['packName']."' WHERE `packid` = ". $_SESSION['packEditId']." AND `userOwns` = ". $_SESSION['isLoggedIn'];
        $db->query($stmt);
        $db->commit();
        $db->close();
        $consoleMsg = "Changes Saved!";
        $consoleMsgType = 0;
    }
    else{
        $consoleMsg = "Please select a pack to edit first!";
        $consoleMsgType = 1;
    }
}

if (isset($_REQUEST['cancelPackEdits'])) {
    header("Refresh:0"); // Refresh Page
}

if(isset($_REQUEST['deletePack']) && isset($_REQUEST['packSelected'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    };
    $stmt = "UPDATE `tracks` SET `packContaining` = NULL WHERE `packid` = ". $_REQUEST['packSelected'];
    $db->query($stmt);
    $db->commit();
    $stmt = "DELETE FROM `packs` WHERE `packid` = ". $_REQUEST['packSelected']." AND `userOwns` = ". $_SESSION['isLoggedIn'];;
    $db->query($stmt);
    $db->commit();
    $db->close();
}

if (isset($_REQUEST['editPack']) && isset($_REQUEST['packSelected'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT * FROM `packs` WHERE `packid` = ". $_REQUEST['packSelected'];
    $trackResult = $db->query($stmt);
    $val = $trackResult->fetch_assoc();
    $packName = $val['packName'];
    $_SESSION['packEditId'] = $val['packid'];
    $db->close();
}
else if(isset($_SESSION['packEditId'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT * FROM `packs` WHERE `packid` = ". $_SESSION['packEditId'];
    $packResult = $db->query($stmt);
    if ($packResult->num_rows != null){
        $val = $packResult->fetch_assoc();
        $packName = $val['packName'];
    }
    $db->close();
}

$db = new mysqli('localhost', 'root', '', 'waveform');
if (mysqli_connect_errno()) {
    echo "Error: Could Not Connect To Database";
    die();
}
$stmt = "SELECT * FROM `packs` WHERE `userOwns` = ".$_SESSION['isLoggedIn'];
$tracksResult = $db->query($stmt);
if (!$tracksResult){
    echo $db->error;
}

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
    <link rel="stylesheet" href="../css/userFilesManage.css">
    <link rel="stylesheet" href="../css/userFilesManagePacks.css">
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
<form action="userFilesManagePacks.php" method="post">
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
                <i class="fa fa-upload"></i> <span>Pack Selector</span>
            </div>
            <div class="trackSelector">
                <div class="trackSelectorCell tSC">
                    <div class="trackSelectorCellIndex1 tSH">

                    </div>
                    <div class="packSelectorCellIndex2 tSH">
                        <span>Pack Name</span>
                    </div>
                    <div class="trackSelectorCellIndex5 tSH">
                        Extra
                    </div>
                </div>
                <?php
                for($i = 0 ; $i < $tracksResult->num_rows ; $i++){
                    $row = $tracksResult->fetch_assoc();
                    echo '
                        <div class="trackSelectorCell">
                        <div class="trackSelectorCellIndex1">
                            <input type="radio" name="packSelected" value="'.$row['packid'].'">
                        </div>
                        <div class="packSelectorCellIndex2">
                            <span>'.$row['packName'].'</span>
                        </div>
                        <div class="trackSelectorCellIndex5">
                            <div class="packSelectorButtons">
                                <a href="link.php?packId='.$row['packid'].'"><div class="button"><i class="fa fa-external-link"></i></div></a>
                            </div>
                        </div>
                    </div>
                    ';
                }
                ?>
            </div>
            <div class="manageTracksButtonsContainer">
                <div class="manageTracksButtons">
                    <input type="submit" id="deletePack" name="deletePack" value="Delete">
                    <input type="submit" id="editPack" name="editPack" value="Edit">
                </div>
            </div>
        </div>
        <div class="filesRightMenu">
            <div class="filesCenterMenuTitle">
                <i class="fa fa-pencil"></i> <span>Pack Editor</span>
            </div>
            <div class="filesCenterMenuSubTitle">
                <i class="fa fa-file-audio-o"></i><span>Pack Selected: <?php echo $packName ?></span>
            </div>
            <div class="filesCenterMenuSubTitle">
                <i class="fa fa-info"></i><span>Pack Information</span>
            </div>
            <div class="manageRightMenuSection" style="margin-top: 30px;">
                <div class="filesCenterMenuTable">
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="packName">Pack Name:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <input type="text" name="packName" id="packName" placeholder="Pack Name" value="<?php echo $packName ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="trackEditButtons">
                <div style="height: 50px">
                    <span class="trackEditConsole" <?php echo ($consoleMsgType == 1 ? 'style="color: #e68282;"' : ""); ?>><?php echo $consoleMsg ?></span>
                </div>
                <input id="savePackEdits" type="submit" name="packEditSave" value="Save Changes">
                <input id="cancelPackEdits" type="submit" name="packEditCancel" value="Roll Back">
            </div>
        </div>
    </div>
</form>
<script>
    function copyTrackLink(packId){
        let input = document.createElement('input');
        input.setAttribute('value', packId);
        document.body.appendChild(input);
        input.select();document.execCommand('copy');
        document.body.removeChild(input);
    }
</script>
</body>
</html>

