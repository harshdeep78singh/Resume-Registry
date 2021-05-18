<?php
    require_once "pdo.php";
    session_start();
    if(isset($_POST["search"])){
        $_SESSION["search"]=$_POST["search"];
        header("location:index.php");
        return;
    }
    if(!isset($_SESSION["search"]))
    {
        $_SESSION["search"]="";
    }
    if(isset($_POST["refresh"])){
        $_SESSION["search"]="";
        header("location:./index.php");
    }
    if(isset($_GET["page"]))
    {
        $page = $_GET["page"];
    }
    else
    {
        $page = 1;
    }
    $limit=($page-1)*10;
?>
<html>
    <head>
        <title>Harshdeep Singh</title>
        <script>
            function next(){
                location.href = './index.php?page=<?php echo ($page+1)?>';
            }
            function back(){
                location.href = './index.php?page=<?php echo ($page-1)?>';
            }
        </script>
        <style>
            .container {
                padding-right: 15px;
                padding-left: 15px;
                margin-right: 400px;
                margin-left: 160px;
                margin-top: 70px; 
            }
            .family{
                margin-bottom:5px;
                margin-top:8px;
            }
            a{
                text-decoration: none;
                color: #337ab7;
            }
            h1{
                margin-bottom:10px;
            }
            td,th{
                text-align: center;
                padding: 5px;
            }
            .heading:hover{
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div class=container>
            <form action="index.php" method="post">
            <h1 class=heading onclick="document.getElementById('ref').click();">Harshdeep Singh's Resume Registry</h1>
            <input type="submit" name="refresh" id="ref" value="refresh" style="display:none">
            </form>
            <form action="index.php" method="post">
                <b style="font-size:18">Search :</b> <input type="text" id="sbox" value="<?php echo $_SESSION['search']?>" name="search" onchange="document.getElementById('search').submit();" placeholder="Search by name..." style="margin:10 0 10 0;width:300px;height:27px;"/>
                <input type="submit" value="search" id="search" style="display : none;height : 27px;"/>
            </form>
            <?php
                if(isset($_SESSION["error"])){
                    echo "<pre style='color:red'>".$_SESSION["error"]."/pre";
                    unset($_SESSION["error"]);
                }
                if(isset($_SESSION["status"])){
                    echo "<pre style='color:green'>".$_SESSION["status"]."</pre>";
                    unset($_SESSION["status"]);
                }
                if(!isset($_SESSION["name"])){
                    echo "<p><a href='login.php'>Please log in</a><span style='margin-left:400px'></span><a href='signup.php'>Sign up</a></p>";
                    $count = $pdo->query("SELECT count(*) FROM profile ");
                        $count = $count->fetch(PDO::FETCH_ASSOC);
                        if((int)$count["count(*)"]%10==0){
                            $tot_pages = ((int)$count["count(*)"]/10);
                        }
                        else{
                            $tot_pages = ((int)($count["count(*)"]/10))+1;
                        }        
                        $sql = $pdo->query("SELECT * FROM profile where first_name LIKE '%".$_SESSION["search"]."%' or last_name like '%".$_SESSION["search"]."%' or headline like '%".$_SESSION["search"]."%' order by profile_id limit ".$limit.",10;");
                        if($sql->rowCount()>0){
                            echo "<table border=1>";
                            echo "<tr><th>Name</th><th>Headline</th></tr>";
                            while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                                echo "<tr><td><a href='view.php?profile_id=".htmlentities($row["profile_id"])."'>".htmlentities($row["first_name"])." ".htmlentities($row["last_name"])."</a></td>";
                                echo "<td>".htmlentities($row["headline"])."</td></tr>";
                            }
                            echo "</table>";
                            if($count["count(*)"]>10)
                            { 
                                if($page>1){
                                    echo "<button name='back' onclick='back()'>Back</button>";
                                }
                                else{
                                    echo "<button type='button' disabled>Back</button>";
                                }
                                if($page<$tot_pages){
                                    echo "<button name='next' onclick='next()'>Next</button>";
                                }
                                else{
                                    echo "<button type='button' disabled>Next</button>";
                                }
                            }
                    }
                    else{
                        echo "no rows found";
                    }
                }
                if(isset($_SESSION["name"])){ 
                        $count = $pdo->query("SELECT count(*) FROM profile where first_name LIKE '%".$_SESSION["search"]."%' or last_name like '%".$_SESSION["search"]."%' or headline like '%".$_SESSION["search"]."%';");
                        $count = $count->fetch(PDO::FETCH_ASSOC);
                        if((int)$count["count(*)"]%10==0){
                            $tot_pages = ((int)$count["count(*)"]/10);
                        }
                        else{
                            $tot_pages = ((int)($count["count(*)"]/10))+1;
                        }        
                        $sql = $pdo->query("SELECT * FROM profile where first_name LIKE '%".$_SESSION["search"]."%' or last_name like '%".$_SESSION["search"]."%' or headline like '%".$_SESSION["search"]."%' order by profile_id limit ".$limit.",10;");
                        if($sql->rowCount()>0){
                            echo "<table border=1>";
                            echo "<tr><th>Name</th><th>Headline</th><th>Action</th></tr>";
                            while($row=$sql->fetch(PDO::FETCH_ASSOC)){
                                echo "<tr><td><a href='view.php?profile_id=".htmlentities($row["profile_id"])."'>".htmlentities($row["first_name"])." ".htmlentities($row["last_name"])."</a></td>";
                                echo "<td>".htmlentities($row["headline"])."</td>";
                                echo "<td><a href='edit.php?profile_id=".htmlentities($row["profile_id"])."'>Edit</a>/<a href='delete.php?profile_id=".htmlentities($row["profile_id"])."'>Delete</a></td></tr>";
                            }
                            echo "</table>";
                            if($tot_pages>1)
                            { 
                                if($page>1){
                                    echo "<button name='back' onclick='back()'>Back</button>";
                                }
                                else{
                                    echo "<button type='button' disabled>Back</button>";
                                }
                                if($page<$tot_pages){
                                    echo "<button name='next' onclick='next()'>Next</button>";
                                }
                                else{
                                    echo "<button type='button' disabled>Next</button>";
                                }
                            }
                    }
                    else{
                        echo "no rows found";
                    }
                    echo "<div style='margin-bottom:10px;margin-top:5px;'><a href='logout.php'>Logout</a></div>";
                    echo "<a href='add.php'>Add New Entry</a><br>";
                }  
            ?>
        </div>
            </body>   
</html>