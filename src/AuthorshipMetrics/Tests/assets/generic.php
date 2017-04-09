<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sergiy
 * Date: 08.04.2017
 * Time: 23:14
 */

die('It is asset. Do not use me :)');

class ClassUnderTest{
    public function functionUnderTest($a){
        $a = 'String under test';
        if(strlen($a) == 0){
            die('waaat?<?php  die("HELP!!"); ?>');
        } else{    
            echo("All fine!");
        }
    }
}
