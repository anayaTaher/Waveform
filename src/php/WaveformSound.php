<link href="../fontawesome/public/css/font-awesome.min.css" rel="stylesheet">
<link href="../css/waveformSound.css" rel="stylesheet">
<script src="https://unpkg.com/wavesurfer.js"></script>
<?php

class WaveformSound{
    private string $username;
    private string $profilePicture;
    private string $trackName;
    private string $packName = "";
    private string $trackAudio;
    private int $id;
    private int $packId;
    private int $userId;
    private int $isPreview = 0;

    public function setUserId($x){
        $this->userId = $x;
        $db = new mysqli('localhost', 'root', '', 'waveform');
        $stmt = "SELECT `username`, `profilePicture` FROM `users` WHERE `userid` = $this->userId";
        $resultQuery = $db->query($stmt);
        $result = $resultQuery->fetch_assoc();
        $this->username = $result['username'];
        $this->profilePicture = ($result['profilePicture'] == NULL ? "" : $result['profilePicture']);
    }
    public function setTrackName($x){
        $this->trackName = $x;
    }
    public function setPackId($id){
        $this->packId = $id;
        $db = new mysqli('localhost', 'root', '', 'waveform');
        $stmt = "SELECT `packName` FROM `packs` WHERE `packid` = $id";
        $resultQuery = $db->query($stmt);
        $result = $resultQuery->fetch_assoc();
        $this->packName = $result['packName'];
    }
    public function setTrackAudio($x){
        $this->trackAudio = $x;
    }
    public function setId($x){
        $this->id = $x;
    }
    public function isPreview($isPreview){
        $this->isPreview = $isPreview;
    }
    public function display(){
        if($this->isPreview == 0){
            echo '<div class="wavesurfer">';
        }
        else if ($this->isPreview == 1){
            echo '<div class="wavesurfer" style="width: 90%;">';
        }
        else if ($this->isPreview == 2){
            echo '<div class="wavesurfer" style="width: 50%;">';
        }
        echo '<div class="upperSection">
                <div class="trackDetails">
                    <span>'.$this->trackName.'</span>';
        if($this->packName != "")
            echo '<span> - <a href="link.php?packId='.$this->packId.'">'.$this->packName.'</a></span>';
        echo '
              </div>
                <a href="'.$this->trackAudio.'" download><div class="downloadButton"><i class="fa fa-download"></i></div></a>
                </div>
                <div class="soundBox">
                    <div class="container" id="container'.$this->id.'"></div>
                    <span id="soundTime'.$this->id.'">00:00</span>
                    <div id="playPause'.$this->id.'" class="iconContainer"><i id="playPauseIcon'.$this->id.'" class="fa fa-play"></i></div>
                    <div id="repeat'.$this->id.'" class="iconContainer"><i id="repeatIcon'.$this->id.'" class="fa fa-repeat"></i></div>
                </div>
                <div class="userInfo">
                    <img src="'.($this->profilePicture == null ? "../../images/avatar.png" : $this->profilePicture).'" alt="Avatar">
                    <span><a href="userProfile.php?userid='.$this->userId.'">'.$this->username.'</a></span>
                </div>
            </div>
        <script>
            function createWaveformSound(){
                let playing = false;
                let playPause = document.getElementById("playPause'.$this->id.'");
                let playPauseIcon = document.getElementById("playPauseIcon'.$this->id.'");
                let repeat = document.getElementById("repeat'.$this->id.'");
                let time = document.getElementById("soundTime'.$this->id.'");
                
                let wavesurfer = WaveSurfer.create({
                    container: \'#container'.$this->id.'\',
                    waveColor: \'#e0e0e0\',
                    progressColor: \'#5193b5\',
                    cursorColor: \'#479eca\',
                    height: \'60\'
                });
                wavesurfer.setVolume(0.05);
                wavesurfer.load(\''.$this->trackAudio.'\');
            
                wavesurfer.on(\'ready\', function(){
                    const timeInSeconds = Math.floor(wavesurfer.getDuration());
                    const minutes = Math.floor(timeInSeconds / 60);
                    const seconds = timeInSeconds%60;
                    time.innerHTML = (minutes >= 10 ? minutes : "0" + minutes) + ":" + (seconds >= 10 ? seconds : "0" + seconds);
                });
            
                playPause.onclick = () => {
                    if (playing){
                        playPauseIcon.className = \'fa fa-play\';
                        playing = false;
                    }
                    else{
                        playPauseIcon.className = \'fa fa-pause\';
                        playing = true;
                    }
                    wavesurfer.playPause();
                }
            
                repeat.onclick = () => {
                    wavesurfer.seekTo(0);
                    wavesurfer.play();
                    playPauseIcon.className = \'fa fa-pause\';
                    playing = true;
                }
            
                wavesurfer.on(\'seek\', function(){
                    if(playing){
                        wavesurfer.play();
                    }
                });
            }
            createWaveformSound();
        </script>';
    }
}
?>
