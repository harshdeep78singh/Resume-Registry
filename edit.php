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
    $sql = "SELECT * FROM position where profile_id=:pid order by rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ":pid" => $_GET["profile_id"]
    ));
    $result1=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_POST["Edit"])){
        if(strlen($_POST["first_name"])>0&&strlen($_POST["last_name"])>0&&strlen($_POST["email"])>0&&strlen($_POST["headline"])>0&&strlen($_POST["summary"])>0){
            if(strpos($_POST["email"],'@')!=FALSE){
                if($_POST["count"]>=1){
                    for($i=1; $i<=$_POST["count"]; $i++) {
                        if ( ! isset($_POST['year'.$i]) ) continue;
                        if ( ! isset($_POST['desc'.$i]) ) continue;
                    
                        $year = $_POST['year'.$i];
                        $desc = $_POST['desc'.$i];
                    
                        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                            $_SESSION["error"] = "All fields are required";
                            header("Location:./edit.php?profile_id=".$_GET["profile_id"]);
                            return;
                        }
                    
                        if ( ! is_numeric($year) ) {
                            $_SESSION["error"] = "Position year must be numeric";
                            header("Location:./edit.php?profile_id=".$_GET["profile_id"]);
                            return;
                        }
                      }
                }
                if($_FILES["fileToUpload"]["tmp_name"]==""){
                    try{
                        $sql = "UPDATE profile set first_name=:fnm,last_name=:lnm,email=:em,headline=:hdln,summary=:sumry WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"],
                            ':fnm' => $_POST["first_name"],
                            ':lnm' => $_POST["last_name"],
                            ':em' => $_POST["email"],
                            ':hdln' => $_POST["headline"],
                            ':sumry' => $_POST["summary"]
                        ));
                        $rank=1;
                        $sql = "DELETE FROM position WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"]
                        ));
                        if($_POST["count"]>=1){
                            for($i=1;$i<=$_POST["count"];$i++){
                                $year = $_POST['year'.$i];
                                $desc = $_POST['desc'.$i];
                                $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                                $stmt->execute(array(
                                ':pid' => $_GET["profile_id"],
                                ':rank' => $rank,
                                ':year' => $year,
                                ':desc' => $desc)
                                );

                                $rank++;
                            }   
                        }
                    }
                    catch(Exception $ex){
                        $_SESSION["error"] = "Internal server error";
                        error_log("sql_error=".$ex->getMessage());
                        header("Location:./edit.php?profile_id=".$_GET["profile_id"]);
                        return;
                    }
                }
                else{
                    try{
                        if(isset($_SESSION["img_to_delete"])){
                            unlink("./profile_pictures/".$_SESSION["img_to_delete"]);
                            unset($_SESSION["img_to_delete"]);
                        }    
                        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],"C:\\xampp\htdocs\Week3\profile_pictures\\".$_FILES["fileToUpload"]["name"]);
                        $sql = "UPDATE profile set first_name=:fnm,last_name=:lnm,email=:em,headline=:hdln,summary=:sumry,image_name=:imgn,image_path=:imgp WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"],
                            ':fnm' => $_POST["first_name"],
                            ':lnm' => $_POST["last_name"],
                            ':em' => $_POST["email"],
                            ':hdln' => $_POST["headline"],
                            ':sumry' => $_POST["summary"],
                            ':imgn' => $_FILES["fileToUpload"]["name"],
                            ':imgp' => "C:\\xampp\htdocs\Week3\profile_pictures\\".$_FILES["fileToUpload"]["name"]
                        ));
                        $rank=1;
                        $sql = "DELETE FROM position WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"]
                        ));
                        if($_POST["count"]>=1){
                            for($i=1;$i<=$_POST["count"];$i++){
                                $year = $_POST['year'.$i];
                                $desc = $_POST['desc'.$i];
                                $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                                $stmt->execute(array(
                                ':pid' => $_GET["profile_id"],
                                ':rank' => $rank,
                                ':year' => $year,
                                ':desc' => $desc)
                                );

                                $rank++;
                            }   
                        }
                    }
                    catch(Exception $ex){
                        $_Session["error"] = "Internal server error";
                        error_log("sql_error=".$ex->getMessage());
                        header("Location:./edit.php?profile_id=".$_GET["profile_id"]);
                        return;
                    }
                }    
                $_SESSION["status"] = "Your Resume is successfully updated";
                header("Location:./index.php");
                return;
            }
            else{
                $_SESSION["error"]="@ required in email";
                header("Location:./edit.php?profile_id=".$_GET["profile_id"]);
                return;
            }
        }
        else{
            $_SESSION["error"]="all fields must be filled";
            header("Location:./edit.php?profile_id=".$_GET["profile_id"]);
            return;
        }
    }
?>

<html>
    <head>
        <title>Harshdeep Singh</title>
        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
        <script>
            function perview_upload(event){
                var reader = new FileReader();
                reader.onload = function()
                {
                var output = document.getElementById('output_image');
                output.src = reader.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
            count=<?php echo count($result1)+1;?>;
            counts=<?php echo count($result1)+1;?>;
            function addPosition(){
                $('#pos').append(
                    '<div id="position'+count+'"> \
                    Year <input type="text" name="year'+count+'" id="year'+count+'"/> <input type="button" id="remove'+count+'" name="remove" value="-" style="font-size:15px" onclick="removePos('+count+')"/> \
                    <br> <textarea name="desc'+count+'" id="desc'+count+'" rows=8 cols=80 style="margin-bottom:20px"></textarea>\
                    </div>'
                );
                count++;
                document.getElementById("count").value=counts;
                counts++;
            }

            function removePos(x){
                $("#position"+x).remove();
                counts--;
                document.getElementById("count").value=counts-1;
                for(i=x;i<count-1;i++){
                    document.getElementById("position"+(i+1)).id="position"+i;
                    document.getElementById("year"+(i+1)).name="year"+i;
                    document.getElementById("year"+(i+1)).id="year"+i;
                    document.getElementById("desc"+(i+1)).name="desc"+i;
                    document.getElementById("desc"+(i+1)).id="desc"+i;
                    document.getElementById("remove"+(i+1)).setAttribute( "onClick", "removePos("+i+")");
                    document.getElementById("remove"+(i+1)).id="remove"+i;
                }
                count--;
            }
        </script>
        </script>
        <style>
            .container {
                padding-right: 15px;
                padding-left: 15px;
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
            .anchor:hover{
                color:green;
                cursor: pointer;
            }
            .pointer{
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <table border=0>
        <tr><td>
        <div class = container>    
            <h1>Editing profile for <?php echo $_SESSION["name"];?></h1>
            <pre style="color:red"><?php
                if(isset($_SESSION["error"])){
                    echo $_SESSION["error"];
                    unset($_SESSION["error"]);
                }
                $sql = "SELECT * FROM profile where profile_id=:pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ":pid" => $_GET["profile_id"]
                ));
                $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
                $result=$result[0];
            ?></pre>
            <form href="add.php" method="POST" enctype="multipart/form-data">
                First Name : <input type="text" name="first_name" style="width:500px" value="<?php echo htmlentities($result["first_name"]);?>"/><br>
                Last Name : <input type="text" name="last_name" style="width:500px" value="<?php echo htmlentities($result["last_name"]);?>"/><br>
                Email : <input type="text" name="email" style="width:250px" value="<?php echo htmlentities($result["email"]);?>"/> <br>
                Headline : <br><input type="text" name="headline" style="width:700px" value="<?php echo htmlentities($result["headline"]);?>"/><br>
                Summary : <br><textarea name="summary" rows="8" cols="80" ><?php echo htmlentities($result["summary"]);?></textarea><br>
                Position: <input type="button" name="position" value="+" style="font-size:20px" onclick="addPosition()"/><br>
                <div id="pos">
                <?php
                for($i=0;$i<count($result1);$i++){
                    echo "<div id='position".$result1[$i]["rank"]."'/>";
                    echo "Year <input type='text' name='year".$result1[$i]["rank"]."' id='year".$result1[$i]["rank"]."' value='".htmlentities($result1[$i]["year"])."'/>"; 
                    echo "<input type='button' id='remove".$result1[$i]["rank"]."' name='remove' value='-' style='font-size:15px' onclick='removePos(".$result1[$i]["rank"].")'/>";
                    echo "<br> <textarea name='desc".$result1[$i]["rank"]."' id='desc".$result1[$i]["rank"]."' rows='8' cols='80' style='margin-bottom:20px'>".htmlentities($result1[$i]["description"])."</textarea>";
                    echo "</div>";
                }
                ?>
                </div>
                <input type=hidden name="count" id="count" value="<?php echo count($result1);?>"/>
                <input type="file" name="fileToUpload" style='display:none' onchange="perview_upload(event);" id="fileToUpload">
                <input type="submit" name="Edit" value="Save"/>
                <input type="button" name="Cancel" value="Cancel" onclick="location.href='index.php';"/><br>
            </form>
        </div>
        </td>
        <td>
            <a onclick="document.getElementById('fileToUpload').click();">
            <?php if($result["image_path"]!=NULL){  
                    $_SESSION["img_to_delete"]=$result["image_name"];
                    echo "<table class='pointer anchor'><tr><td style='border: 1px solid black;width:150px; height:200px;text-align:center;border-radius: 50px 50px 50px 50px;'>";
                    echo "<img id='output_image' src='./profile_pictures/".$result["image_name"]."' alt='no profile image' border=0 style='width:160px; height:210px;align:center;border-radius: 50px 50px 50px 50px;'/>";                            
                    echo "</td>";
                    echo "</tr>";
                    echo "</table>";
                }
                else{
                    echo "<table class='pointer anchor'><tr><td style='border: 1px solid black;width:150px; height:200px;text-align:center;border-radius: 50px 50px 50px 50px;'>";
                    echo "<img id='output_image' border=0 style='width:160px; height:210px;align:center;border-radius: 50px 50px 50px 50px;'/>";
                    echo "<svg style='position:absolute;top:22em; right:33.5em;' xmlns='http://www.w3.org/2000/svg' width='25' height='25' fill='currentColor' class='bi bi-camera-fill' viewBox='0 0 16 16'>";
                    echo "<path d='M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z'/>";
                    echo "<path d='M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z'/>";
                    echo "</svg>";
                    echo "</td>";
                    echo "</tr>";
                    echo "</table>";
                }   
            ?>
            </a>
        </td> 
        </tr>
        </table>   
    </body>
</html>