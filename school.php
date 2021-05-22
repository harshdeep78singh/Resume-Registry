<?php
    if(!isset($_GET["term"])){
        die("missing required parameter");
    }
    session_start();
    if(!isset($_SESSION["user_id"])){
        die("ACCESS DENIED");
    }
    require_once "pdo.php";
    $sql = $pdo->prepare("SELECT * from institution where name like :prefix");
    $sql->execute(array(
        ':prefix' => $_GET["term"].'%'
    ));

    $resul=array();
    while($row = $sql->fetch(PDO::FETCH_ASSOC)){
        $result[] = $row["name"]; 
    }
    echo(json_encode($result,JSON_PRETTY_PRINT));
?>