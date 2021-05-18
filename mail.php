<?php
    $to = "harshdeep78singh@gmail.com";
    $subject = "testing";
    $message = "testing";
    $header = "From:manan3236@gmail.com";

    try{
        if(mail($to,$subject,$message,$header)){
            echo "mail sent";
        }
        else{
        echo "cannot send mail";
        }
    }
    catch(Exception $e){
        echo $e->getMessage();
    }
?>