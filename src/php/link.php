<?php

$result = [];
$resultSize = -1;

if(isset($_GET['packId'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT * FROM `packs` INNER JOIN `tracks` ON `packid` = `packContaining` WHERE `packid` = ".$_GET['packId'];
    $result = $db->query($stmt);
    $resultSize = $result->num_rows;
    echo $db->error;
}

if(isset($_GET['trackId'])){
    $db = new mysqli('localhost', 'root', '', 'waveform');
    if (mysqli_connect_errno()) {
        echo "Error: Could Not Connect To Database";
        die();
    }
    $stmt = "SELECT * FROM `tracks` WHERE `trackid` = ".$_GET['trackId'];
    $result = $db->query($stmt);
    $resultSize = $result->num_rows;
    echo $db->error;
}
include_once ("WaveformSound.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>waveform - Link</title>
    <link href="../../images/waveform.svg" rel="icon">
    <style>
        body {
            background-color: #40485E;
        }
        body > div {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            display: flex;
            align-items: center;
            flex-direction: column;
        }
        body > div > *{
            margin-top: 20px;
        }
        span {
            font-family: "Century Gothic", serif;
            font-size: 24pt;
            color: #e0e0e0;
        }
    </style>
</head>
<body>
<div>
    <?php
    if ($resultSize < 1){
        echo '<span>There\'s noting here to see</span>';
    }
    for($i = 0 ; $i < $resultSize ; $i++){
        $row = $result->fetch_assoc();
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
</body>
</html>

