<?php

class Login
{
    private $errorMsg = "";

    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
    }

    public function display()
    {
        echo '
                <div id="loginMenu">
        <div class="centerElementsCenter" >
            <form action="index.php" method="post">
                <table id="menuTable">
                    <tr>
                        <td>
                            <img id="loginMenuPic" src="../../images/waveform.svg" alt="">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding-top: 20px">
                            <span id="loginMsg">Please Log In<br></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding-top: 20px">
                            <span id="loginMsg1">By using your waveform account<br><br></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
            ';
        $x = new TextField();
        $x->setName("loginUsername");
        $x->setPrompt("Username");
        $x->display();
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>
                ';
        $x = new TextField();
        $x->setName("loginPassword");
        $x->setPrompt("Password");
        $x->isPassword(true);
        $x->display();
        echo '
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a class="forgotPass" href="requestReset.php">Forgot password?</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">
                            <div class="centerElementsLeft">
                                <input id="rememberMe2" type="checkbox" name="rememberMe" value="yes">
                                <label id="rememberMe" for="rememberMe2">Remember me</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px;">
                            <input id="loginButton" type="submit" value="Log In">
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding-top: 10px">
                ';
        echo "<span id=\"console\"> $this->errorMsg </span>";
        echo '                
                        </td>
                    </tr>
                        <td style="text-align: center; padding-top: 50px">
                            <span id="loginMsg1">Or by using your social media account<br><br></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="socialMedia">
                                <i class="fa fa-google"></i>
                                <i class="fa fa-facebook"></i>
                                <i class="fa fa-twitter"></i>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
            ';
    }
}
