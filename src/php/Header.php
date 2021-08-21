<style>
    <?php
    include_once ("../../src/css/index.css");
    include_once ("../../src/fontawesome/src/FontAwesome.php");
    ?>
</style>

<?php
class Header{
    private $isLoggedIn = false;
    private $username;
    private $profilePicture;

    public function isLoggedIn($x){
        $this->isLoggedIn = $x;
    }

    public function setUsername($x){
        $this->username = $x;
    }

    public function setProfilePicture($x){
        $this->profilePicture = $x;
    }

    public function display(){
        echo '
        <div class="background">
            <a href="index.php"><img class="logoImg" src="../../images/waveform.svg" height="75px" width="75px" alt=""></a>
            <a href="index.php"><span class="logoName">waveform</span></a>
            <ul>
                <li style="margin-left: 100px"><i class="fa fa-home"></i><a href="index.php">Homepage</a></li>
                <li><i class="fa fa-search"></i><a href="search.php">Search</a></li>
                <li><i class="fa fa-phone"></i><a href="contactUs.php">Contact Us</a></li>
                <li><i class="fa fa-support"></i><a href="support.php">Support</a></li>
            </ul>
            <div class="menuDivLeft">';
        if (!$this->isLoggedIn){
            echo '
                <ul>
                    <li><a onclick="openLoginPage()"><button id="login" type="button" class="buttonClass">Login</button></li>
                    <li><a onclick="openSignUpPage()"><button id="signup" type="button" class="buttonClass">Sign Up</button></a></li>
                </ul>
            ';
        }
        else{
            echo '
                <div class="dropdown"><ul>';
            if ($this->profilePicture == null){
                echo '<li><img src="../../images/avatar.png" id="topProfilePicture" alt="Avatar"></li>';
            }
            else{
                echo '<li><img src="'.$this->profilePicture.'" id="topProfilePicture" alt="Avatar"></li>';
            }
            echo '
                <li id="topUserName">'.$this->username.'</li>
                </ul>';
        }
        echo '
                <div class="dropdown-content" id="dropDown">
                    <a href="userProfile.php?userid='.$_SESSION['isLoggedIn']. '"><i class="fa fa-user"></i>Profile</a>
                    <a href="userSettings.php"><i class="fa fa-cog"></i>Settings</a>
                    <a href="userFilesUpload.php"><i class="fa fa-file"></i>Files</a>
                    <div></div>
                    <a href="logout.php"><i class="fa fa-sign-out"></i>Logout</a>
                </div>
                </div>
                </div>
            </div>';
    }
}