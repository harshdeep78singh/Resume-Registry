<?php
    session_start();
    require_once "pdo.php";
    require_once "util.php";
    if(!isset($_GET["profile_id"])||strlen($_GET["profile_id"])<1){
        die("profile_id missing");
    }
    if(!isset($_SESSION["name"])){
        die("Not logged in");
    }
    $sql = $pdo->prepare("SELECT * FROM profile where user_id = :u_id");
    $sql->execute(array(
        'u_id' => $_SESSION["user_id"]
    ));
    $check=false;
    while($row=$sql->fetch(PDO::FETCH_ASSOC)){
        if($_GET["profile_id"]!=$row["profile_id"]){
            continue;
            $check=false;
        }
        else{
            $check=true;
            break;
        }
    }
    if($check==false){
        die("ACCESS DENIED");
    }
    $sql = "SELECT * FROM position where profile_id=:pid order by rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ":pid" => $_GET["profile_id"]
    ));
    $result1=$stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = "SELECT * FROM education where profile_id=:pid order by rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ":pid" => $_GET["profile_id"]
    ));
    $result2=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(isset($_POST["Edit"])){
        if(strlen($_POST["first_name"])>0&&strlen($_POST["last_name"])>0&&strlen($_POST["email"])>0&&strlen($_POST["headline"])>0&&strlen($_POST["summary"])>0){
            if(strpos($_POST["email"],'@')!=FALSE){
                if(validatepos()==false){
                    header("Location:./add.php");
                    return;
                }
                if(validateedu()==false){
                    header("Location:./add.php");
                    return;
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
                        $sql = "DELETE FROM education WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"]
                        ));
                        $profile_id = $_GET["profile_id"];
                        $sql = $pdo->prepare('Insert into position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
                        for($i=1;$i<=$_POST["countp"];$i++){
                            $year=$_POST["year".$i];
                            $desc=$_POST["desc".$i];
                            $sql->execute(array(
                                ':pid' => $profile_id,
                                ':rank' => $i,
                                ':year' => $year,
                                ':desc' => $desc
                                )
                            );
                        }
                        for($i=1;$i<=$_POST["counte"];$i++){
                            $year=$_POST["yer".$i];
                            $stmt = $pdo->prepare("SELECT institution_id from institution where name=:nm");
                            $stmt->execute(array(
                                ':nm' => $_POST["sc".$i]
                            ));
                            if($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                                $desc=$row["institution_id"];
                            }
                            else{
                                $stmt=$pdo->prepare("INSERT into institution (name) values(:nm)");
                                $stmt->execute(array(
                                    ':nm' => $_POST["sc".$i]
                                ));
                                $stmt = $pdo->prepare("SELECT institution_id from institution where name=:nm");
                                $stmt->execute(array(
                                    ':nm' => $_POST["sc".$i]
                                ));
                                $row=$stmt->fetch(PDO::FETCH_ASSOC);
                                $desc=$row["institution_id"];
                            }
                            $sql = $pdo->prepare('Insert into education (profile_id, rank, year, institution_id) VALUES ( :pid, :rank, :year, :desc)');
                            $sql->execute(array(
                                ':pid' => $profile_id,
                                ':rank' => $i,
                                ':year' => $year,
                                ':desc' => $desc
                            ));
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
                        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],"C:\\xampp\htdocs\Week4\profile_pictures\\".$_FILES["fileToUpload"]["name"]);
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
                            ':imgp' => "C:\\xampp\htdocs\Week4\profile_pictures\\".$_FILES["fileToUpload"]["name"]
                        ));
                        $rank=1;
                        $sql = "DELETE FROM position WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"]
                        ));
                        $sql = "DELETE FROM education WHERE profile_id=:pid";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(array(
                            ':pid' => $_GET["profile_id"]
                        ));
                        $profile_id = $_GET["profile_id"];
                        $sql = $pdo->prepare('Insert into position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
                        for($i=1;$i<=$_POST["countp"];$i++){
                            $year=$_POST["year".$i];
                            $desc=$_POST["desc".$i];
                            $sql->execute(array(
                                ':pid' => $profile_id,
                                ':rank' => $i,
                                ':year' => $year,
                                ':desc' => $desc
                                )
                            );
                        }
                        for($i=1;$i<=$_POST["counte"];$i++){
                            $year=$_POST["yer".$i];
                            $stmt = $pdo->prepare("SELECT institution_id from institution where name=:nm");
                            $stmt->execute(array(
                                ':nm' => $_POST["sc".$i]
                            ));
                            if($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                                $desc=$row["institution_id"];
                            }
                            else{
                                $stmt=$pdo->prepare("INSERT into institution (name) values(:nm)");
                                $stmt->execute(array(
                                    ':nm' => $_POST["sc".$i]
                                ));
                                $stmt = $pdo->prepare("SELECT institution_id from institution where name=:nm");
                                $stmt->execute(array(
                                    ':nm' => $_POST["sc".$i]
                                ));
                                $row=$stmt->fetch(PDO::FETCH_ASSOC);
                                $desc=$row["institution_id"];
                            }
                            $sql = $pdo->prepare('Insert into education (profile_id, rank, year, institution_id) VALUES ( :pid, :rank, :year, :desc)');
                            $sql->execute(array(
                                ':pid' => $profile_id,
                                ':rank' => $i,
                                ':year' => $year,
                                ':desc' => $desc
                            ));
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
        <link rel="stylesheet"
            href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
            integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
            crossorigin="anonymous">

        <link rel="stylesheet" 
            href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" 
            integrity="sha384-xewr6kSkq3dBbEtB6Z/3oFZmknWn7nHqhLVLrYgzEFRbU/DHSxW7K3B44yWUN60D" 
            crossorigin="anonymous">

        <script
        src="https://code.jquery.com/jquery-3.2.1.js"
        integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
        crossorigin="anonymous"></script>

        <script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
        integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
        crossorigin="anonymous"></script>
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
            pcount=<?php echo count($result1)+1 ?>;
            ecount=<?php echo count($result2)+1 ?>;
            console.log(ecount,pcount);
            function addeducation(){
                $("#edu").append(
                    "<div style='margin-top:15px;' id='edu"+ecount+"'>  \
                        <pre>Year : </pre><input type=text class='inputsize' style='width:200px;' name='yer"+ecount+"' id='yer"+ecount+"' >\
                        <input type=button style='font-size:15px;' id='but"+ecount+"' value=- onclick='removeedu("+ecount+");' /><br>  \
                        <pre>School : </pre><input type=text value='' style='width:300px' class='inputsize school' name='sc"+ecount+"' id='sc"+ecount+"'>  \
                    </div>"
                );
                ecount++;
                $('.school').autocomplete({
                    source: "school.php"
                });
                console.log(ecount,pcount);
            }
            $( document ).ready(function() {
                $('.school').autocomplete({
                    source: "school.php"
                });
            });
            function addposition(){
                $("#pos").append(
                    "<div style='margin-top:15px;' id='pos"+pcount+"'>  \
                        <pre>Year : </pre><input type=text class='inputsize' name='year"+pcount+"' id='year"+pcount+"' style='width:200px;' name='Year'>  \
                        <input type=button style='font-size:15px;' id='butt"+pcount+"' value='-' onclick='removepos("+pcount+");' /><br>  \
                        <textarea style='width:600px;height:150px;' name='desc"+pcount+"' id='desc"+pcount+"' ></textarea>  \
                    </div>"
                );
                pcount++;
                console.log(ecount,pcount);
            }

            function removepos(x){
                $('#pos'+x).remove();
                pcount--;
                for(i=x;i<pcount;i++){
                    document.getElementById('pos'+(i+1)).id='pos'+i;
                    document.getElementById('year'+(i+1)).id='year'+i;
                    document.getElementById('desc'+(i+1)).id='desc'+i;
                    document.getElementById('butt'+(i+1)).setAttribute('onclick','removepos('+i+');');
                    document.getElementById('butt'+(i+1)).id='butt'+i;
                    document.getElementById('pos'+(i)).name='pos'+i;
                    document.getElementById('year'+(i)).name='year'+i;
                    document.getElementById('desc'+(i)).name='desc'+i;
                }
                console.log(ecount,pcount);
            }
            function removeedu(x){
                $('#edu'+x).remove();
                ecount--;
                for(i=x;i<ecount;i++){
                    document.getElementById('edu'+(i+1)).id = 'edu'+i;
                    document.getElementById('yer'+(i+1)).id = 'yer'+i;
                    document.getElementById('sc'+(i+1)).id = 'sc'+i;
                    document.getElementById('but'+(i+1)).setAttribute('onclick','removeedu('+i+');');
                    document.getElementById('but'+(i+1)).id = 'but'+i;
                    document.getElementById('edu'+(i)).name = 'edu'+i;
                    document.getElementById('yer'+(i)).name = 'yer'+i;
                    document.getElementById('sc'+(i)).name = 'sc'+i;
                }
                console.log(ecount,pcount);
            }
            function getcount(){
                document.getElementById("countp").value = pcount-1;
                document.getElementById("counte").value = ecount-1;
                return true;
            } 
        </script>
        <style>
            .contain {
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
                height:150px;
                width:700px;
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
                margin-top:135px;
            }
            pre{
                font-size : 20;
                display : inline;
                margin-top:100px;
            }
            .inputsize{
                height:30px;
                width:500px;
            }
            button{
                height:10px;
            }
        </style>
    </head>
    <body>   
            <table border=0>
                <tr><td>
                <div class="contain">     
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
                        <pre>First Name : </pre><input type="text" name="first_name" style="width:500px" value="<?php echo htmlentities($result["first_name"]);?>" /><br>
                        <pre>Last Name : </pre><input type="text" name="last_name" style="width:500px" value="<?php echo htmlentities($result["last_name"]);?>"/><br>
                        <pre>Email : </pre><input type="text" name="email" style="width:250px" value="<?php echo htmlentities($result["email"]);?>"/> <br>
                        <pre>Headline : </pre><br><input type="text" name="headline" style="width:700px" value="<?php echo htmlentities($result["headline"]);?>"/><br>
                        <pre>Summary : </pre><br><textarea name="summary" rows="8" cols="80" ><?php echo htmlentities($result["summary"]);?></textarea><br>
                        <pre>Position: </pre><input type="button" name="position" value="+" style="font-size:20px" onclick="addposition()"/><br>
                        <div id="pos">
                            <?php
                            for($i=0;$i<count($result1);$i++){
                                echo "<div style='margin-top:15px;' id='pos".$result1[$i]["rank"]."'>  ";
                                echo "<pre>Year : </pre><input type=text class='inputsize' name='year".$result1[$i]["rank"]."' id='year".$result1[$i]["rank"]."' style='width:200px;' value='".htmlentities($result1[$i]["year"])."' name='Year'>  ";
                                echo "<input type=button style='font-size:15px;' id='butt".$result1[$i]["rank"]."' value='-' onclick='removepos(".$result1[$i]["rank"].");' /><br>  ";
                                echo "<textarea style='width:600px;height:150px;' name='desc".$result1[$i]["rank"]."' id='desc".$result1[$i]["rank"]."' >".htmlentities($result1[$i]["description"])."</textarea>  ";
                                echo "</div>";
                            }
                            ?>
                        </div>
                        <pre>Education: </pre><input type="button" name="education" value="+" style="font-size:20px" onclick="addeducation()"/><br>
                        <div id="edu">
                            <?php
                            for($i=0;$i<count($result2);$i++){
                                $sql = "SELECT name FROM institution where institution_id=:iid";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(array(
                                    ":iid" => $result2[$i]["institution_id"]
                                ));
                                $result3=$stmt->fetch(PDO::FETCH_ASSOC);
                                echo "<div style='margin-top:15px;' id='edu".$result2[$i]["rank"]."'>";
                                echo "<pre>Year : </pre><input type=text class='inputsize' style='width:200px;' value='".$result2[$i]["year"]."' name='yer".$result2[$i]["rank"]."' id='yer".$result2[$i]["rank"]."' >";
                                echo "<input type=button style='font-size:15px;' id='but".$result2[$i]["rank"]."' value=- onclick='removeedu(".$result2[$i]["rank"].");' /><br> " ;
                                echo "<pre>School : </pre><input type=text  value='".$result3["name"]."' style='width:300px' class='inputsize school' name='sc".$result2[$i]["rank"]."' id='sc".$result2[$i]["rank"]."'> " ;
                                echo "</div>";
                                #echo "<script>";
                                #echo "$('.school').autocomplete({";
                                #echo "source: 'school.php'";
                                #echo "});";
                                #echo "</script>";
                            }
                            ?>
                        </div>
                        <input type=hidden name="countp" id="countp"/>
                        <input type=hidden name="counte" id="counte"/>
                        <input type="file" name="fileToUpload" style='display:none' onchange="perview_upload(event);" id="fileToUpload">
                        <input type="submit" name="Edit" value="Save" onclick="return getcount();"/>
                        <input type="button" name="Cancel" value="Cancel" onclick="location.href='index.php';"/><br>
                    </form>
                </td>
                </div>
                <td style="vertical-align:top">
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
                            echo "<table style='margin-top:190' class='pointer anchor'><tr><td style='border: 1px solid black;width:150px; height:200px;text-align:center;border-radius: 50px 50px 50px 50px;'>";
                            echo "<img id='output_image' border=0 style='width:160px; height:210px;align:center;border-radius: 50px 50px 50px 50px;'/>";
                            echo "<svg style='position:absolute;top:21em; right:32.5em;' xmlns='http://www.w3.org/2000/svg' width='25' height='25' fill='currentColor' class='bi bi-camera-fill' viewBox='0 0 16 16'>";
                            echo "<path d='M10.5 8.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z'/>";
                            echo "<path d='M2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2zm.5 2a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm9 2.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0z'/>";
                            echo "</svg>";
                            echo "</td>";
                            echo "</tr>";
                            echo "</table>";
                        }   
                    ?>
                    </a>
                </td></tr>
            </table>   
    </body>
</html>