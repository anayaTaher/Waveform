<?php
$db = new mysqli('localhost', 'root', '', 'waveform');

if (mysqli_connect_errno()) {
    echo "Error: Could Not Connect To Database";
    die();
}