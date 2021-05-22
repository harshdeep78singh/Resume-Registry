<?php
    session_start();
    $_SESSION["POST"]=$_POST;
    if(isset($_FILES)){
        $_SESSION["FILES"]=$_FILES;
    }
    require_once "pdo.php";
    require_once "util.php";
    if(isset($_SESSION["user_id"])){
        if(isset($_POST["Add"])){
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
                    if($_FILES["fileToUpload"]["tmp_name"]!=""){
                        try{
                            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],"C:\\xampp\htdocs\Week4\profile_pictures\\".$_FILES["fileToUpload"]["name"]);
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
                                ':imgp' => "C:\\xampp\htdocs\Week4\profile_pictures\\".$_FILES["fileToUpload"]["name"]
                            ));
                            $profile_id = $pdo->lastInsertId();
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
                $_SESSION["error"]="All fields are required";
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
            var ecount=1
            var pcount=1
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
            }
            function addposition(){
                $("#pos").append(
                    "<div style='margin-top:15px;' id='pos"+pcount+"'>  \
                        <pre>Year : </pre><input type=text class='inputsize' name='year"+pcount+"' id='year"+pcount+"' style='width:200px;' name='Year'>  \
                        <input type=button style='font-size:15px;' id='butt"+pcount+"' value='-' onclick='removepos("+pcount+");' /><br>  \
                        <textarea style='width:600px;height:150px;' name='desc"+pcount+"' id='desc"+pcount+"' ></textarea>  \
                    </div>"
                );
                pcount++;
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
                    <pre>First Name : </pre><input type=text class="inputsize" name="first_name">
                    <pre>Last Name  : </pre><input type=text class="inputsize" name="last_name">
                    <pre>Email : </pre><input type=text class="inputsize" style="width:300px" name="email">
                    <pre>Headline : </pre><input type=text class="inputsize" style="width:600px" name="headline"><br>
                    <pre>Summary : </pre><br><textarea name=summary></textarea><br>
                    <input type="file" name="fileToUpload" style="display:none;" onchange="perview_upload(event);" id="fileToUpload">
                    <div style="margin-top:4px;">
                        <pre>Education : </pre>
                        <input type=button style="font-size:20px;" value=+ onclick="addeducation();" /><br>
                        <div id=edu></div>
                    </div>
                    <div style="margin-bottom:4px;">
                        <pre>Position : </pre>
                        <input type=button style="font-size:20px;" value=+ onclick="addposition();" /><br>
                        <div id=pos></div>
                    </div>
                    <input type=hidden id=countp name=countp />
                    <input type=hidden id=counte name=counte />
                    <input type=submit value=Add name=Add onclick="return getcount();"/>
                    <input type=button value=Cancel onclick="location.href='index.php'" />
                </form>
            </td>
        </div>
                <td class=image >
                    <a onclick="document.getElementById('fileToUpload').click();">
                    <table class="pointer anchor"><tr><td style="border: 1px solid black;width:150px; height:200px;text-align:center;border-radius: 50px 50px 50px 50px;">
                        <img id="output_image" border=0 style="width:160px; height:210px;align:center;border-radius: 50px 50px 50px 50px;"/>
                        <svg style="position:absolute;top:20em; right:31em;" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-camera-fill" viewBox="0 0 16 16">
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