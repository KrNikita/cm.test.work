<?php

class MessageObject{

    public $messageId = "";
    public $date = "";
    public $from = "";
    public $subject = "";

    public function __construct(string $message_id,$date,$from,$subject){
        $this->messageId = $message_id;
        $this->date = $date;
        $this->from = $from;
        $this->subject = $subject;
    }
}