<?php
    session_start();
    $_SESSION["POST"]=$_POST;
    if(isset($_FILES)){
        $_SESSION["FILES"]=$_FILES;
    }
    require_once "pdo.php";
    if(isset($_SESSION["name"])){
        if(isset($_POST["Add"])){
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
                                header("Location:./add.php");
                                return;
                            }
                        
                            if ( ! is_numeric($year) ) {
                                $_SESSION["error"] = "Position year must be numeric";
                                header("Location:./add.php");
                                return;
                            }
                          }
                    }
                    if($_FILES["fileToUpload"]["tmp_name"]!=""){
                        try{
                            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],"C:\\xampp\htdocs\Week3\profile_pictures\\".$_FILES["fileToUpload"]["name"]);
                            $sql = "INSERT into profile (user_id,	first_name,last_name,email,headline,summary,image_name,image_path) values(:uid,:fnm,:lnm,:em,:hdln,:sumry,:imgn,:imgp)";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(array(
                                ':uid' => $_SESSION["user_id"],
                                ':fnm' => $_POST["first_name"],
                                ':lnm' => $_POST["last_name"],
                                ':em' => $_POST["email"],
                                ':hdln' => $_POST["headline"],
                                ':sumry' => $_POST["summary"],
                                ':imgn' => $_FILES["fileToUpload"]["name"],
                                ':imgp' => "C:\\xampp\htdocs\Week3\profile_pictures\\".$_FILES["fileToUpload"]["name"]
                            ));
                            $profile_id = $pdo->lastInsertId();
                            $rank=1;
                            if($_POST["count"]>=1){
                                for($i=1;$i<=$_POST["count"];$i++){
                                    $year = $_POST['year'.$i];
                                    $desc = $_POST['desc'.$i];
                                    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                                    $stmt->execute(array(
                                    ':pid' => $profile_id,
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
                            header("Location:./add.php");
                            return;
                        }    
                        $_SESSION["status"] = "Your Resume is Registered";
                        header("Location:./index.php");
                        return;
                    }
                    else{
                        try{
                            $sql = "INSERT into profile (user_id,	first_name,last_name,email,headline,summary) values(:uid,:fnm,:lnm,:em,:hdln,:sumry)";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute(array(
                                ':uid' => $_SESSION["user_id"],
                                ':fnm' => $_POST["first_name"],
                                ':lnm' => $_POST["last_name"],
                                ':em' => $_POST["email"],
                                ':hdln' => $_POST["headline"],
                                ':sumry' => $_POST["summary"]
                            ));
                            $profile_id = $pdo->lastInsertId();
                            $rank=1;
                            if($_POST["count"]>=1){
                                for($i=1;$i<=$_POST["count"];$i++){
                                    $year = $_POST['year'.$i];
                                    $desc = $_POST['desc'.$i];
                                    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');

                                    $stmt->execute(array(
                                    ':pid' => $profile_id,
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
                            header("location:add.php");
                            return;
                        }    
                        $_SESSION["status"] = "Added";
                        header("Location:./index.php");
                        return;
                    }    
                }
                else{
                    $_SESSION["error"]="@ required in email";
                    header("Location:./add.php");
                    return;
                }
            }
            else{
                $_SESSION["error"]="All values are required";
                header("Location:./add.php");
                return;
            }
        }
    }
    else{
        die("Not logged in");
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
            count=1;
            counts=1;
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
        <style>
            .contain {
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
            .image{
                vertical-align:top;
                padding-left:20px;
                padding-top:60px;
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
        <div class = contain>  
            <table border=0>
                        <tr> <td> 
                <h1>Adding profile for <?php echo $_SESSION["name"];?></h1>
                <?php
                    if(isset($_SESSION["error"])){
                        echo "<pre style='color:red'>";
                        echo $_SESSION["error"];
                        unset($_SESSION["error"]);
                        echo "</pre>";
                    }
                ?>
                <form href="add.php" method="POST" enctype="multipart/form-data">
                    First Name : <input type="text" name="first_name" style="width:500px"/><br>
                    Last Name : <input type="text" name="last_name" style="width:500px"/><br>
                    Email : <input type="text" name="email" style="width:250px"/><br>
                    Headline : <br><input type="text" name="headline" style="width:700px"/><br>
                    Summary : <br><textarea name="summary" rows="8" cols="80"></textarea><br>
                    <input type="file" name="fileToUpload" style="display:none;" onchange="perview_upload(event);" id="fileToUpload">
                    Position: <input type="button" name="position" value="+" style="font-size:20px" onclick="addPosition()"/><br>
                    <div id="pos"></div>
                    <input type=hidden name="count" id="count" value="0"/>
                    <input type="submit" name="Add" value="Add"/>
                    <input type="button" name="Cancel" value="Cancel" onclick="location.href='index.php';"/><br>
                </form>
            </td>
        </div>
                <td class=image >
                    <a onclick="document.getElementById('fileToUpload').click();">
                    <table class="pointer anchor"><tr><td style="border: 1px solid black;width:150px; height:200px;text-align:center;border-radius: 50px 50px 50px 50px;">
                        <img id="output_image" border=0 style="width:160px; height:210px;align:center;border-radius: 50px 50px 50px 50px;"/>
                        <svg style="position:absolute;top:20em; right:33em;" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-camera-fill" viewBox="0 0 16 16">
                            <path d="M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                            <path d="M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z"/>
                        </svg>
                    </td>
                    </tr>
                    </table>
                    </a>
                </td>   
            </tr>
        </table>
    </body>
</html>