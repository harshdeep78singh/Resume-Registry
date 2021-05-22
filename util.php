<?php
    function validatepos(){
        if($_POST["countp"]>=1){
            for($i=1; $i<=$_POST["countp"]; $i++) {
                if ( ! isset($_POST['year'.$i]) ) continue;
                if ( ! isset($_POST['desc'.$i]) ) continue;
            
                $year = $_POST['year'.$i];
                $desc = $_POST['desc'.$i];

                if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                    $_SESSION["error"] = "All fields are required";
                    return false;
                }
            
                if ( ! is_numeric($year) ) {
                    $_SESSION["error"] = "Position year must be numeric";
                    return false;
                }
            }
        }
        return true;
    }

    function validateedu(){
        if($_POST["counte"]>=1){
            for($i=1; $i<=$_POST["counte"]; $i++) {
                if (!isset($_POST['yer'.$i]) ) continue;
                if (!isset($_POST['sc'.$i]) ) continue;
            
                $year = $_POST['yer'.$i];
                $desc = $_POST['sc'.$i];
            
                if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                    $_SESSION["error"] = "All fields are required";
                    return false;
                }
            
                if ( ! is_numeric($year) ) {
                    $_SESSION["error"] = "Education year must be numeric";
                    return false;
                }
            }
        }
        return true;
    }
?>

        