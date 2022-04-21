<?php

$data = file_get_contents("php://input");
$event = json_decode($data, true);
if(isset($event)){
    //Here, you now have event and can process them how you like e.g Add to the database or generate a response
    $file = 'log.txt';  
    $data =json_encode($event)."\n";  
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);}
