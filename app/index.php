<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../src/Database.php";
require_once "../src/DBUtils.php";

//Initialize database and tables if no exists
$db = new Database();
$db->Query("CREATE DATABASE IF NOT EXISTS test;");

$db->Query("CREATE TABLE IF NOT EXISTS messages(".
                "ID BIGINT NOT NULL AUTO_INCREMENT,".
                "InternetID VARCHAR(250) CHARACTER SET latin1 NOT NULL,".
                "RecvDate VARCHAR(250) CHARACTER SET latin1 NOT NULL,".
                "Subject VARCHAR(250) CHARACTER SET utf8 NOT NULL,".
                "FromAddress VARCHAR(250) CHARACTER SET latin1 NOT NULL,".
                "PRIMARY KEY (ID),".
                "INDEX InternetIDIndex (InternetID));");

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Clean Mail Test Project</title>
    <script src="index.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <div class="left" style="text-align: center;">
        <h1>Microsoft API Version</h1>

        <button onclick="location.href='/oauth.php'">Connect to Microsoft Account</button>
        <br/><br/>
        <?php if(isset($_SESSION['access_token'])):?>
            <button onclick="loadMessagesViaMsGraphAPI('<?=$_SESSION['access_token']?>',this);">Load messages GraphAPI</button>
        <?php endif;?>
    </div>


    <div class="right" style="text-align: center;">
        <h1>IMAP Version</h1>

        <input type="text" id="username_id" placeholder="Email">
        <br/><br/>
        <input type="password" id="password_id" placeholder="Password">
        <br/><br/>
        <button onclick="loadMessagesFromIMAPMicrosoftServer(document.getElementById('username_id').value,
                                                             document.getElementById('password_id').value,this);">
            Load messages from Microsoft IMAP
        </button>
    </div>
</div>
<div class="clear"></div>

<br/>
<br/>
<br/>
<br/>

<div style="text-align: center;">
    <button onclick="UpdateMessagesList(this);">Update messages list</button>
</div>

<div id="result_container_id" class="container"></div>

<script>
    document.body.onload = function(){
        UpdateMessagesList();
    }
</script>

</body>
</html>