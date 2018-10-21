<?php
require_once '../src/Database.php';
require_once '../src/DBUtils.php';

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

if(isset($_POST['action']) && $_POST['action'] == "get_mails_thru_imap"){
    //Loading messages from microsoft IMAP server
    if(!isset($_POST['username']) || !isset($_POST['password'])){
        print("Username or Password not found.");
        exit;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    require "../src/IMAPMailListCollector.php";

    try {
        $db = new Database();
        $mailListCollector = new IMAPMailListCollector();
        $mail_list = $mailListCollector->GetMessagesHeadersList();
        if($mail_list != FALSE) {
            DBUtils::addMessagesToDatabase($db, $mail_list);
            echo count($mail_list) . " messages loaded";
        }
    }catch (Exception $e){
        print($e->GetMessage().PHP_EOL);
    }
}
else if(isset($_POST['action']) && $_POST['action'] == "load_messages_ms_graph_api"){
    //Load messages from mictosoft mailbox thru GraphAPI
    if(isset($_POST['accessToken'])){
        require '../src/MicrosoftAPIMailListCollector.php';

        $db = new Database();
        $mailListCollector = new MicrosoftAPIMailListCollector($_POST['accessToken']);
        $mail_list = $mailListCollector->GetMessagesHeadersList();
        if($mail_list != FALSE) {
            DBUtils::addMessagesToDatabase($db, $mail_list);
            echo count($mail_list) . " messages loaded";
        }
    }
}
else if(isset($_POST['action']) && $_POST['action'] == "get_messages_list"){
    //Get messages list from database
    $db = new Database();
    $results = $db->SelectQuery("SELECT InternetID,RecvDate,Subject,FromAddress FROM messages;");
    foreach ($results as $r){
        $recv_date = $r['RecvDate'];
        $subject = $r['Subject'];
        $from = $r['FromAddress'];
        include "ListElement.tpl.htm";
    }
}
