<?php

require_once "IMailListCollector.php";
require_once "MessageObject.php";

class IMAPMailListCollector implements IMailListCollector
{
    private const server = '{outlook.office365.com:993/imap/ssl}INBOX';
    private const username = 'lebron.mail@gmail.com';
    private const password = 'Carmelo1MS2';

    private $connection = FALSE;
    private $msgCount = 0;

    public function __construct(){
        //Connect to server
        $this->connection = imap_open($this::server, $this::username, $this::password, OP_READONLY);
        if($this->connection == FALSE) {
            $this->ProcessErrors();
            throw new Exception('Connection Error');
        }

        $check = imap_check($this->connection);
        if($check == FALSE) {
            $this->ProcessErrors();
            throw new Exception('Inbox Check Error');
        }

        $this->msgCount = $check->Nmsgs;
    }

    public function __destruct(){
        if($this->connection != FALSE ) {
            imap_close($this->connection);
        }
    }

    public function GetMessagesHeadersList(){
        $msgListArray = new SplFixedArray($this->msgCount);

        for($i=0; $i<$this->msgCount; $i++) {
            $header = imap_headerinfo($this->connection, $i+1);
            if($header == FALSE){
                $this->ProcessErrors();
                return FALSE;
            }
            $msgListArray[$i] = new MessageObject($header->message_id,$header->date,$header->fromaddress,$header->subject);
        }
        return $msgListArray;
    }

    public function ProcessErrors(){
        $errors = imap_errors();
        if(count($errors)> 0){
            print("Errors list:".PHP_EOL);

            foreach ($errors as $e){
                print("\t".$e.PHP_EOL);
            }
        }
    }
}