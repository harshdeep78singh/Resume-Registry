<?php
    $salt="XyZzy12*_";
    $pass="1a52e17fa899cf40fb04cfc42e6352f1";
    require_once "pdo.php";
    session_start();
    if(isset($_POST["login"])){
        if(strlen($_POST["email"])>0&&strlen($_POST["pass"])>0){
            if(strpos($_POST["email"],'@')!=FALSE){
                $hash=hash("md5",$salt.$_POST["pass"]);
                $sql=$pdo->query("SELECT * FROM users");
                $check=FALSE;
                while($row = $sql->fetch(PDO::FETCH_ASSOC)){
                    if($_POST["email"]==$row["email"] && $hash==$row["password"]){
                        $check=TRUE;
                        break;
                    }
                }
                if($check==TRUE){
                    $_SESSION["name"]=$row["name"];
                    $_SESSION["user_id"]=$row["user_id"];
                    error_log("login success ".$_POST["email"]);
                    session_write_close();
                    header("location:./index.php");
                    return;
                }
                else{
                    error_log("login fail ".$_POST["email"]." ".hash("md5",$salt.$_POST["pass"]));
                    $_SESSION["error"]="incorrect credentials";
                    header("location:./login.php");
                    return;
                }
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
            <h1>Please Log In</h1>
            <pre style="color:red"><?php
                    if(isset($_SESSION["error"])){
                        echo $_SESSION["error"];
                        unset($_SESSION["error"]);
                    }
                ?></pre>
            <form href="login.php" method="POST">
                <pre class=family><b>User Name  </b><input type="text" name="email" id="email"/><br></pre>
                <pre class=family><b>Password  </b><input type="password" name="pass" id="pass"/><br></pre>
                <input type="submit" name="login" value="Log In" onclick="return doValidate();"/>
                <input type="button" value="cancel" onclick="location.href = './index.php';"/>
            </form>
        </div>
        <script>
            function doValidate(){
                try{
                    console.log('Validating...');
                    aw=document.getElementById("email").value;
                    pw=document.getElementById("pass").value;
                    console.log("Validating email ="+aw);
                    if(aw== null||aw=="")
                    {
                        alert("Both fields must be filled out");
                        return false;
                    }
                    if(aw.indexOf('@')==-1){
                        alert("invalid email address");
                        return false;
                    }
                    console.log("Validating password="+pw);
                    if(pw== null||pw=="")
                    {
                        alert("Both fields must be filled out");
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