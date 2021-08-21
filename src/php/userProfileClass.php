<?php
include_once("../../src/fontawesome/src/FontAwesome.php");
include_once("WaveformSound.php");

class userProfile
{
    private $userName = "", $About = "", $Release = "", $profilePicture;
    private $mediaTwitter = "", $mediaPatreon = "", $mediaFacebook = "";
    private $contactInformationGmail = "", $contactInformationDiscord = "";
    private $userid = -1;

    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    public function setAbout($About)
    {
        if ($About == null)
            $About = "There are only insects here :(";
        $this->About = $About;
    }

    public function setRelease($Release)
    {
        if ($Release == null)
            $Release = "There are only insects here :(";
        $this->Release = $Release;
    }

    public function setMediaTwitter($mediaTwitter)
    {
        if ($mediaTwitter == null)
            $mediaTwitter = "No link has been assigned yet :(";
        $this->mediaTwitter = $mediaTwitter;
    }

    public function setMediaPatreon($mediaPatreon)
    {
        if ($mediaPatreon == null)
            $mediaPatreon = "No link has been assigned yet :(";
        $this->mediaPatreon = $mediaPatreon;
    }

    public function setMediaFacebook($mediaFacebook)
    {
        if ($mediaFacebook == null)
            $mediaFacebook = "No link has been assigned yet :(";
        $this->mediaFacebook = $mediaFacebook;
    }

    public function setContactInformationGmail($contactInformationGmail)
    {
        if ($contactInformationGmail == null)
            $contactInformationGmail = "No link has been assigned yet :(";
        $this->contactInformationGmail = $contactInformationGmail;
    }

    public function setContactInformationDiscord($contactInformationDiscord)
    {
        if ($contactInformationDiscord == null)
            $contactInformationDiscord = "No link has been assigned yet :(";
        $this->contactInformationDiscord = $contactInformationDiscord;
    }

    public function setUserId($userid)
    {
        $this->userid = $userid;
    }

    public function display()
    {
        echo '
   
<div class="main">
    <table id="mainTable">
        <tr>
            <td id="firstColumn">
            </td>
            <td style="width: 80%; height: 100%; background-color: #40485E; ">
                <div id="divToButton" style="height: 70px;">
                    <button id="about" type="button">About</button>
                    <button id="release" type="button">Releases</button>
                </div>
                <div style="display: block" id="aboutDiv">
                    <span>' . $this->About . '</span>
                </div>
                <div style=" display: none;" id="releaseDiv">';
                    $db = new mysqli('localhost', 'root', '', 'waveform');
                    if (mysqli_connect_errno()) {
                        echo "Error: Could Not Connect To Database";
                        die();
                    }
                    $stmt = "SELECT * FROM `tracks` WHERE `userUploaded` = $this->userid AND `private` = 0";
                    $res = $db->query($stmt);
                    if(!$res){
                        echo $db->error;
                    }
                    for($i = 0 ; $i < $res->num_rows ; $i++){
                        $row = $res->fetch_assoc();
                        $x = new WaveformSound();
                        $x->setId($row['trackid']);
                        $x->setUserId($row['userUploaded']);
                        $x->setTrackName($row['trackName']);
                        $x->setTrackAudio($row['trackDir']);
                        $x->isPreview(2);
                        if ($row['packContaining'] != NULL){
                            $x->setPackId($row['packContaining']);
                        }
                        $x->display();
                    }
                echo'</div>
            </td>
        </tr>
    </table>
    <div class="leftMain">
        <div id="leftContainer">';
        if ($this->profilePicture == null) {
            echo ' <img id="mainProfilePicture" src="../../images/avatar.png" alt="">';
        } else {
            echo ' <img id="mainProfilePicture" src="' . $this->profilePicture . '" alt="">';
        }
        echo '
            <span id="mainProfileName"> ' . $this->userName . ' </span>
            <dl id="media">
                <dt><i class="fa fa-link"></i><span>Media</span></dt>
                <dd><i class="fa fa-twitter"></i><span>' . $this->mediaTwitter . '</span></dd>
                <dd><img src="../../images/patreon-brands.svg" style="width: 10pt; height: 10pt" alt=""><span>' . $this->mediaPatreon . '</span></dd>
                <dd><i class="fa fa-facebook"></i><span>' . $this->mediaFacebook . '</span></dd>
            </dl>
            <dl id="contactInformation">
                <dt><i class="fa fa-phone"></i><span>Contact Information</span></dt>
                <dd><i class="fa fa-google"></i><span>' . $this->contactInformationGmail . '</span></dd>
                <dd><img src="../../images/discord-brands.svg" style="width: 10pt; height: 10pt" alt=""><span>' . $this->contactInformationDiscord . '</span></dd>
            </dl>
        </div>
    </div>
</div>
    ';
    }
}