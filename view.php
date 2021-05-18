<?php
    session_start();
    require_once "pdo.php";
    if(!isset($_GET["profile_id"])||strlen($_GET["profile_id"])<1){
        die("profile_id missing");
    }
?>

<html>
    <head>
        <title>Harshdeep Singh</title>
        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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
            a{
                text-decoration: none;
            }
            .anchor{
                margin-top:6em;
            }
        </style>
    </head>
    <body>
        <table border=0>
            <tr><td>
        <div class = container>    
            <h1>Profile Information</h1>
            <?php
                $sql = "SELECT * FROM profile where profile_id=:pid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ":pid" => $_GET["profile_id"]
                ));
                $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
                $result=$result[0];
            ?>
                <p>First Name : <?php echo htmlentities($result["first_name"]);?></p> 
                <p>Last Name : <?php echo htmlentities($result["last_name"]);?></p>
                <p>Email : <?php echo htmlentities($result["email"]);?></p>
                <p>Headline : <br><?php echo htmlentities($result["headline"]);?></p>
                <p>Summary : <br><?php echo htmlentities($result["summary"]);?></p>
                <?php
                    $sql = "SELECT * FROM position where profile_id=:pid order by rank";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(
                        ":pid" => $_GET["profile_id"]
                    ));
                    $result1=$stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo "<ul>";
                    for($i=0;$i<count($result1);$i++){
                        echo "<li style='margin-bottom:5px'>".$result1[$i]["year"].":".$result1[$i]["description"]."</li>";
                    }
                    echo "</ul>";
                ?>
                <a href="./index.php" style="color: #337ab7">Done</a>
            </td>
            <td>
            <?php if($result["image_path"]!=NULL){  
                    echo "<table class='anchor'><tr><td style='border: 1px solid black;width:150px; height:200px;text-align:center;border-radius: 50px 50px 50px 50px;'>";
                    echo "<img src='./profile_pictures/".$result["image_name"]."' alt='no profile image' border=0 style='width:160px; height:210px;align:center;border-radius: 50px 50px 50px 50px;'/>";                            
                    echo "</td>";
                    echo "</tr>";
                    echo "</table>";
                }   
            ?>    
        </div>
        </td></tr>
    </body>
</html>