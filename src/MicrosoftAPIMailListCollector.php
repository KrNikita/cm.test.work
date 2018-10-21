<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "IMailListCollector.php";
require_once "MessageObject.php";

class MicrosoftAPIMailListCollector implements IMailListCollector
{
    private $access_token = "";
    private $msgCount = 0;
    public function __construct($access_token){
        $this->access_token = $access_token;
    }

    private function Request($url, $headers, $body){
        $curl = curl_init();
        json_encode($body);

        curl_setopt_array($curl,
            array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_FAILONERROR => false
            )
        );

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }

        curl_close($curl);

        return $response;
    }


    public function GetMessagesHeadersList(){

        $messageQueryParams = array (
            "\$select" => "subject,receivedDateTime,from,internetMessageId",
            // Sort by ReceivedDateTime, newest first
            "\$orderby" => "receivedDateTime DESC",
        );

        $res = $this->Request('https://graph.microsoft.com/v1.0/me/messages?'.http_build_query($messageQueryParams),
                                array('Authorization: Bearer ' . $this->access_token), '');

        $result_array = json_decode($res, true);

        if(array_key_exists('value',$result_array)){
            $this->msgCount = count($result_array['value']);

            $msgListArray = array();
            for($i=0;$i<$this->msgCount;$i++){
                $msg = $result_array['value'][$i];

                if(!array_key_exists('internetMessageId',$msg) || $msg['internetMessageId'] == '')continue;

                $from = $msg['from']['emailAddress']['address'];
                $msgListArray[] = new MessageObject($msg['internetMessageId'],$msg['receivedDateTime'],$from,$msg['subject']);
            }
            return $msgListArray;
        }else if(array_key_exists('error',$result_array)){
            $this->ProcessErrors($result_array['error']);
        }
        return FALSE;
    }

    //Process errors recursively
    public function ProcessErrors($error_obj){

        if(array_key_exists('code',$error_obj)){
            echo "Code:".$error_obj['code'].PHP_EOL;
        }

        if(array_key_exists('message',$error_obj)){
            echo "Message:".$error_obj['message'].PHP_EOL;
        }

        //Check for inner
        if(array_key_exists('innerError',$error_obj)){
            $error_obj = $error_obj['innerError'];

            if (array_key_exists('date',$error_obj) &&
                array_key_exists('request-id',$error_obj)){
                echo $error_obj['date'].":".$error_obj['request-id'].PHP_EOL;
            }

            echo PHP_EOL;

            $this->ProcessErrors($error_obj);
        }
    }
}