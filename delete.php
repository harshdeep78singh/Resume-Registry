<?php
    session_start();
    require_once "pdo.php";
    if(!isset($_GET["profile_id"])||strlen($_GET["profile_id"])<1){
        die("profile_id missing");
    }
    if(!isset($_SESSION["name"])){
        die("Not logged in");
    }
    $sql = $pdo->query("SELECT * FROM profile");
    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
        if($_GET["profile_id"]==$row["profile_id"]){
            if($row["user_id"]!=$_SESSION["user_id"]){
                die("ACCESS DENIED");
            }
            else{
                break;
            }
        }
    }
    if(isset($_POST["Delete"])){
        try{
            $check = "SELECT * FROM profile WHERE profile_id=:pid";
            $stmt = $pdo->prepare($check);
            $stmt->execute(array(
                ':pid' => $_GET["profile_id"]
            ));
            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $result=$result[0];
            if($result["image_name"]!=NULL){
                unlink("./profile_pictures/".$result["image_name"]);
                $sql = "DELETE FROM profile WHERE profile_id=:pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':pid' => $_GET["profile_id"]
                ));
                $sql = "DELETE FROM position WHERE profile_id=:pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':pid' => $_GET["profile_id"]
                ));
            }
            else{
                $sql = "DELETE FROM profile WHERE profile_id=:pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':pid' => $_GET["profile_id"]
                ));
                $sql = "DELETE FROM position WHERE profile_id=:pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':pid' => $_GET["profile_id"]
                ));
            }
        }
        catch(Exception $ex){
            $_Session["error"] = "Internal server error";
            error_log("sql_error=".$ex->getMessage());
            header("location:add.php");
            return;
        }    
        $_SESSION["status"] = "Profile Deleted";
        header("Location:./index.php");
        return;
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
            input{
                margin-top:5px;
                margin-bottom: 10px;
                height:27px;
            }
            textarea{
                margin-top:5px;
            }
        </style>
    </head>
    <body>
        <div class = container>    
            <h1>Deleting profile for <?php echo $_SESSION["name"];?></h1>
            <pre style="color:red"><?php
                if(isset($_SESSION["error"])){
                    echo $_SESSION["error"];
                    unset($_SESSION["error"]);
                }
            ?></pre>
            <h3>Are You Sure You Want To Delete This Profile</h3>
            <form href="add.php" method="POST">
                <input type="submit" name="Delete" value="Delete"/>
                <input type="button" name="Cancel" value="Cancel" onclick="location.href='index.php';"/><br>
            </form>
        </div>
    </body>
</html>