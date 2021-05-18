<?php
    $salt="XyZzy12*_";
    require_once "pdo.php";
    session_start();
    if(isset($_POST["signup"])){
        if(strlen($_POST["email"])>0&&strlen($_POST["pass"])>0&&strlen($_POST["uname"])>0){
            if(strpos($_POST["email"],'@')!=FALSE){
                $hash=hash("md5",$salt.$_POST["pass"]);
                try{
                    $sql = "INSERT into users (name,email,password) values(:nm,:em,:pw)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(
                        ':nm' => $_POST["uname"],
                        ':em' => $_POST["email"],
                        ':pw' => $hash
                    ));
                }
                catch(Exception $ex){
                    $_Session["error"] = "Internal server error";
                    error_log("sql_error=".$ex->getMessage());
                    header("location:add.php");
                    return;
                }    
                $_SESSION["status"] = "Account Created Please log in";
                header("Location:./index.php");
                return;
            }
            else{
                $_SESSION["error"]="missing @";
                header("location:./login.php");
                return;
            }
        }
        else{
            $_SESSION["error"]="all fields must be filled";
            header("location:./login.php");
            return;
        }
    }
?>

<html>
    <head>
        <title>Harshdeep Singh</title>
        <style>
            .container {
                padding-right: 15px;
                padding-left: 15px;
                margin-right: 400px;
                margin-left: 160px;
                margin-top: 70px; 
            }
            .family{
                font-family:"sans-serif";
                margin-bottom:5px;
                margin-top:0px;
            }
            input{
                height:27px;
                margin-bottom:5px;
            }
            b{
                font-size:18;
            }
        </style>
    </head>
    <body>
        <div class=container>    
            <h1>Please Sign Up</h1>
            <pre style="color:red"><?php
                    if(isset($_SESSION["error"])){
                        echo $_SESSION["error"];
                        unset($_SESSION["error"]);
                    }
                ?></pre>
            <form href="login.php" method="POST">
                <pre class=family><b>User Name  </b><input type="text" name="uname" id="uname"/><br></pre>
                <pre class=family><b>Email  </b><input type="text" name="email" id="email"/><br></pre>
                <pre class=family><b>Password  </b><input type="password" name="pass" id="pass"/><br></pre>
                <input type="submit" name="signup" value="Sign Up" onclick="return doValidate();"/>
                <input type="button" value="cancel" onclick="location.href = './index.php';"/>
            </form>
        </div>
        <script>
            function doValidate(){
                try{
                    console.log('Validating...');
                    aw=document.getElementById("email").value;
                    pw=document.getElementById("pass").value;
                    uw=document.getElementById("uname").value;
                    console.log("Validating email ="+aw);
                    if(aw== null||aw=="")
                    {
                        alert("All fields must be filled out");
                        return false;
                    }
                    if(aw.indexOf('@')==-1){
                        alert("invalid email address");
                        return false;
                    }
                    console.log("Validating password="+pw);
                    if(pw== null||pw=="")
                    {
                        alert("All fields must be filled out");
                        return false;
                    }
                    console.log("Validating username="+uw);
                    if(uw== null||uw=="")
                    {
                        alert("All fields must be filled out");
                        return false;
                    }
                    return true;
                }
                catch(e){
                    alert(e);
                    return false;
                }
                return false;
            }
        </script>
    </body>
</html>