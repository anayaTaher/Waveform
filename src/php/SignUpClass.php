<?php

class SignUp
{
    private $errorMsg = "";

    public function setErrorMsg($errorMsg)
    {
        $this->errorMsg = $errorMsg;
    }

    public function display()
    {
        echo '
            <div id="signUpMenu">
                <div class="centerElementsCenter" >
                    <form action="index.php" method="post">
                        <table id="menuTable">
                            <tr>
                                <td>
                                    <img id="signUpMenuPicture" src="../../images/waveform.svg" alt="">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding-top: 20px">
                                    <span id="signUpMsg">Sign up<br></span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding-top: 20px">
                                    <span id="signUpMsg1">Fill in all the fields</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 20px">
        ';
        $x = new TextField();
        $x->setName("signUpUsername");
        $x->setPrompt("Username");
        $x->display();
        echo '
                                </td>
                            </tr>
                            <tr>
                                <td>
        ';
        $x = new TextField();
        $x->setName("signUpEmail");
        $x->setPrompt("Email");
        $x->display();
        echo '
                                </td>
                            </tr>
                            <tr>
                                <td>
        ';
        $x = new TextField();
        $x->setName("signUpPassword");
        $x->setPrompt("Password");
        $x->isPassword(true);
        $x->display();
        echo '
                                </td>
                            </tr>
                            <tr>
                                <td>
        ';
        $x = new TextField();
        $x->setName("signUpPasswordConf");
        $x->setPrompt("Confirm Password");
        $x->isPassword(true);
        $x->display();
        echo '
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px;">
                                    <button id="signUpButton" type="button">Sign Up</button>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding-top: 10px">
                                    <span id="console2"> ' . $this->errorMsg . '</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; padding-top: 40px">
                                    <span id="SignUpMsg1">Or use your social media account<br><br></span>
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