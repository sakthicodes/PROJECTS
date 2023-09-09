<?php
function inputFields($placeholder,$name,$value,$type){
    $ele="
    <div class=\"form-group my-4\">
    <input type='$type' name='$name' placeholder='$placeholder' class=\"form-control\" value='$value'>
    </div>
    
    ";
    echo $ele;
}
?>