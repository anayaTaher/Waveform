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

$trackName = "Track Name";
$trackid = -1;
$trackDir = "";
$userUploaded = -1;
$private = 0;
$packContaining = -1;

$consoleMsg = "";
$consoleMsgType = 0;

if(isset($_REQUEST['trackEditSave'])){
    if (isset($_SESSION['trackEditId'])){
        $db = new mysqli('localhost', 'root', '', 'waveform');
        if (mysqli_connect_errno()) {
            echo "Error: Could Not Connect To Database";
            die();
        }
        $stmt = "
            UPDATE `tracks` SET 
            `trackName` = '".$_REQUEST['trackName'] ."',
            `private` = ". (isset($_REQUEST['private']) ? "1" : "0") .",
            `packContaining` = ".($_REQUEST['packSelect'] == "NO" ? "NULL" : $_REQUEST['packSelect']) ."
            WHERE `trackid` = ".$_SESSION['trackEditId']." AND `userUploaded` = ".$_SESSION['isLoggedIn'].";
            ";
        $db->query($stmt);
        $db->commit();
        $db->close();
        $consoleMsg = "Changes Saved!";
        $consoleMsgType = 0;
    }
    else{
        $consoleMsg = "Please select a track to edit first!";
        $consoleMsgType = 1;
    }
}

if (isset($_REQUEST['cancelTrackEdits'])) {
    header("Refresh:0"); // Refresh Page
}

if(isset($_REQUEST['deleteTrack']) && isset($_REQUEST['trackSelected'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT `trackDir` FROM `tracks` WHERE `trackid` = ". $_REQUEST['trackSelected'];
    $trackResult = $db->query($stmt);
    $val = $trackResult->fetch_assoc();
    $dir = $val['trackDir'];
    unlink($dir);
    $stmt = "DELETE FROM `tracks` WHERE `trackid` = ". $_REQUEST['trackSelected']." AND `userUploaded` = ". $_SESSION['isLoggedIn'];;
    $db->query($stmt);
    $db->commit();
    $db->close();
}

if (isset($_REQUEST['editTrack']) && isset($_REQUEST['trackSelected'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT * FROM `tracks` WHERE `trackid` = ". $_REQUEST['trackSelected'];
    $trackResult = $db->query($stmt);
    $val = $trackResult->fetch_assoc();
    $trackName = $val['trackName'];
    $trackid = $val['trackid'];
    $trackDir = $val['trackDir'];
    $userUploaded = $val['userUploaded'];
    $private = $val['private'];
    $packContaining = ($val['packContaining'] == null ? -1 : $val['packContaining']);
    $db->close();
    $_SESSION['trackEditId'] = $trackid;
}
else if(isset($_SESSION['trackEditId'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT * FROM `tracks` WHERE `trackid` = ". $_SESSION['trackEditId'];
    $trackResult = $db->query($stmt);
    if ($trackResult->num_rows != 0){
        $val = $trackResult->fetch_assoc();
        $trackName = $val['trackName'];
        $trackid = $val['trackid'];
        $trackDir = $val['trackDir'];
        $userUploaded = $val['userUploaded'];
        $private = $val['private'];
        $packContaining = ($val['packContaining'] == null ? -1 : $val['packContaining']);
    }
    $db->close();
}

$db = new mysqli('localhost', 'root', '', 'waveform');
if (mysqli_connect_errno()) {
    echo "Error: Could Not Connect To Database";
    die();
}
$stmt = "SELECT * FROM `tracks` INNER JOIN `packs` ON `packid` = `packContaining` WHERE `userUploaded` = ".$_SESSION['isLoggedIn'];
$tracksResult = $db->query($stmt);
if (!$tracksResult){
    echo $db->error;
}
$stmt = "SELECT * FROM `tracks` WHERE `packContaining` IS NULL AND `userUploaded` = ".$_SESSION['isLoggedIn'];
$tracksResultNoP = $db->query($stmt);
if (!$tracksResultNoP){
    echo $db->error;
}

$stmt = "SELECT * FROM `packs` WHERE `userOwns` = ".$_SESSION['isLoggedIn'];
$userPacks = $db->query($stmt);
if (!$userPacks){
    echo $db->error;
}
$db->close();

include_once("Header.php");
include_once("TextField.php");
include_once("WaveformSound.php");
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
<form action="userFilesManageTracks.php" method="post">
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
                <i class="fa fa-upload"></i> <span>Track Selector</span>
            </div>
            <div class="trackSelector">
                <div class="trackSelectorCell tSC">
                    <div class="trackSelectorCellIndex1 tSH">

                    </div>
                    <div class="trackSelectorCellIndex2 tSH">
                        <span>Track Name</span>
                    </div>
                    <div class="trackSelectorCellIndex3 tSH">
                        <span>Pack Name</span>
                    </div>
                    <div class="trackSelectorCellIndex4 tSH">
                        <span>Private</span>
                    </div>
                    <div class="trackSelectorCellIndex5 tSH">
                        Extra
                    </div>
                </div>
                <?php
                for($i = 0 ; $i < $tracksResultNoP->num_rows ; $i++){
                    $row = $tracksResultNoP->fetch_assoc();
                    echo '
                        <div class="trackSelectorCell">
                        <div class="trackSelectorCellIndex1">
                            <input type="radio" name="trackSelected" value="'.$row['trackid'].'">
                        </div>
                        <div class="trackSelectorCellIndex2">
                            <span>'.$row['trackName'].'</span>
                        </div>
                        <div class="trackSelectorCellIndex3">
                            <span style="color: #e68282">NO PACK</span>
                        </div>
                        <div class="trackSelectorCellIndex4 tSH">
                            '.($row['private'] == 1 ? "<span style='color: #e68282'>Private</span>" : "<span style='color: #82e69e'>Public</span>").'
                        </div>
                        <div class="trackSelectorCellIndex5">
                            <div class="trackSelectorButtons">
                                <a href="'.$row['trackDir'].'" download><div class="button"><i class="fa fa-download"></i></div></a>
                                <a href="link.php?trackId='.$row['trackid'].'"><div class="button"><i class="fa fa-external-link"></i></div></a>
                            </div>
                        </div>
                    </div>
                    ';
                }
                for($i = 0 ; $i < $tracksResult->num_rows ; $i++){
                    $row = $tracksResult->fetch_assoc();
                    echo '
                        <div class="trackSelectorCell">
                        <div class="trackSelectorCellIndex1">
                            <input type="radio" name="trackSelected" value="'.$row['trackid'].'">
                        </div>
                        <div class="trackSelectorCellIndex2">
                            <span>'.$row['trackName'].'</span>
                        </div>
                        <div class="trackSelectorCellIndex3">
                            <span>'.$row['packName'].'</span>
                        </div>
                        <div class="trackSelectorCellIndex4 tSH">
                            '.($row['private'] == 1 ? "<span style='color: #e68282'>Private</span>" : "<span style='color: #82e69e'>Public</span>").'
                        </div>
                        <div class="trackSelectorCellIndex5">
                            <div class="trackSelectorButtons">
                                <a href="'.$row['trackDir'].'" download><div class="button"><i class="fa fa-download"></i></div></a>
                                <a href="link.php?trackId='.$row['trackid'].'"><div class="button"><i class="fa fa-external-link"></i></div></a>
                            </div>
                        </div>
                    </div>
                    ';
                }
                ?>
            </div>
            <div class="manageTracksButtonsContainer">
                <div class="manageTracksButtons">
                    <input type="submit" id="deleteTrack" name="deleteTrack" value="Delete">
                    <input type="submit" id="editTrack" name="editTrack" value="Edit">
                </div>
            </div>
        </div>
        <div class="filesRightMenu">
            <div class="filesCenterMenuTitle">
                <i class="fa fa-pencil"></i> <span>Track Editor</span>
            </div>
            <div class="filesCenterMenuSubTitle">
                <i class="fa fa-file-audio-o"></i><span>Track Selected: <?php echo $trackName ?></span>
            </div>
            <div class="wavesurferPreviewer">
                <?php
                if ($trackid != -1){
                    $x = new WaveformSound();
                    $x->setUserId($userUploaded);
                    $x->setId($trackid);
                    $x->isPreview(1);
                    $x->setTrackName($trackName);
                    $x->setTrackAudio($trackDir);
                    if ($packContaining != -1){
                        $x->setPackId($packContaining);
                    }
                    $x->display();
                }
                ?>
            </div>
            <div class="filesCenterMenuSubTitle">
                <i class="fa fa-info"></i><span>Track Information</span>
            </div>
            <div class="manageRightMenuSection" style="margin-top: 30px;">
                <div class="filesCenterMenuTable">
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="trackName">Track Name:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <input type="text" name="trackName" id="trackName" placeholder="Track Name" value="<?php echo $trackName ?>">
                        </div>
                    </div>
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="packSelect">Pack:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <select name="packSelect" id="packSelect">
                                <option value="NO" selected>NONE</option>
                                <?php
                                for($i = 0 ; $i < $userPacks->num_rows ; $i++){
                                    $row = $userPacks->fetch_assoc();
                                    echo '<option value="'.$row['packid'].'" '.($packContaining == $row['packid'] ? "selected" : "") .'>'.$row['packName'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="filesCenterMenuRow">
                        <div class="filesCenterMenuCell1">
                            <label for="private">Private:</label>
                        </div>
                        <div class="filesCenterMenuCell2">
                            <input type="checkbox" name="private" id="private" value="Yes" <?php echo ($private == 1 ? "checked" : "") ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="trackEditButtons">
                <div style="height: 50px">
                    <span class="trackEditConsole" <?php echo ($consoleMsgType == 1 ? 'style="color: #e68282;"' : ""); ?>><?php echo $consoleMsg ?></span>
                </div>
                <input id="saveTrackEdits" type="submit" name="trackEditSave" value="Save Changes">
                <input id="cancelTrackEdits" type="submit" name="trackEditCancel" value="Roll Back">
            </div>
        </div>
    </div>
</form>
<script>
    function copyTrackLink(trackId){
        let input = document.createElement('input');
        input.setAttribute('value', trackId);
        document.body.appendChild(input);
        input.select();document.execCommand('copy');
        document.body.removeChild(input);
    }
</script>
</body>
</html>

