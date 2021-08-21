<?php
include_once ("../../src/fontawesome/src/FontAwesome.php");
?>
<style>
    <?php
        include_once ("../../src/css/index.css"); ?>
</style>
<?php
class TextField{
    private $prompt;
    private $name;
    private $showPrompt = false;
    private $isPassword = false;
    private $value;

    public function setPrompt($string){
        $this->prompt = $string;
    }

    public function setName($string){
        $this->name = $string;
    }

    public function isPassword($x){
        $this->isPassword = $x;
    }

    public function showPrompt($x){
        $this->showPrompt = $x;
    }

    public function setValue($x){
        $this->value = $x;
    }

    public function display(){
        echo '<div class="TextFieldDiv">';
        if($this->showPrompt){
            echo '<label class="TextFieldLabel" for="'.$this->name.'">'.$this->prompt.':</label>';
        }
        if($this->isPassword){
            echo '<input type="password" class="textField" id="'.$this->name.'" name="'.$this->name.'" placeholder="'.$this->prompt.'">';
        }
        else{
            echo '<input type="text" class="textField" id="'.$this->name.'" name="'.$this->name.'" value="'.$this->value.'" placeholder="'.$this->prompt.'">';
        }
        echo '</div>';
    }
}

?>

