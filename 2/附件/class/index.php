<?php
spl_autoload_register(function($class){
    require("./class/".$class.".php");
});
highlight_file(__FILE__);
error_reporting(0);
$action = $_GET['action'];
$properties = $_POST['properties'];
class Action{

    public function __construct($action,$properties){

        $object=new $action();
        foreach($properties as $name=>$value)
            $object->$name=$value;
        $object->run();
        echo '11';
    }
}

new Action($action,$properties);
?>