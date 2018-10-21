<?php

class DBUtils
{
    //Save array of MessageObject`s to Database messages table
    static public function addMessagesToDatabase($db,$messages_array){
        $messages_to_insert = array();
        foreach($messages_array as $msg) {
            $ret = $db->SelectQuery("SELECT InternetID FROM messages WHERE InternetID='" . $msg->messageId . "';");
            if (count($ret) == 0) {
                $messages_to_insert[] = $msg;
            }
        }
        if(count($messages_to_insert) > 0) {
            $insert_query = "INSERT INTO messages(InternetID,RecvDate,Subject,FromAddress) VALUES";

            foreach ($messages_to_insert as $msg) {
                $insert_query .= "('" . $msg->messageId . "','" . $msg->date . "','" . $msg->subject . "','" . $msg->from . "'),";
            }
            $insert_query = substr_replace($insert_query, ";", -1);

            $db->Query($insert_query);
        }
    }
}