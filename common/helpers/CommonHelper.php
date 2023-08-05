<?php
namespace common\helpers;
use Yii;
use \paragraph1\phpFCM\Recipient\Device;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommonHelper
 *
 * @author smart
 */
class CommonHelper {
    //put your code here
    static function getDefaultDate($date = null, $format="default", $args = []){
        if($date){
            return date("Y-m-d H:i:s", strtotime($date));
        }
        return date("Y-m-d H:i:s", time());
    }

    
    static function updateModelDefault($model = []){  
        if($model){
            $model->updateddate = CommonHelper::getDefaultDate();        
            if($model->isNewRecord){
                $model->createddate = $model->createddate ? $model->createddate : CommonHelper::getDefaultDate();
            }
            $model->updateddate = CommonHelper::getDefaultDate();
            if(Yii::$app->user->getId()){
                if($model->isNewRecord)
                    $model->createdby = Yii::$app->user->getId();
                    $model->updatedby = Yii::$app->user->getId();
            } else{
                $model->createdby = 1;
                $model->updatedby = 1;
            }  
            return $model;
        }
     }
     
     
     /**
      * 
      * @param type $data
      * @param type $header
      * @param type $filename
      * @throws Exception
      * @uses For Genrate Excel File
      * @author Bhavandeep Singh <bhavandeep@digimantra.com>
    */
     static function printExcel($data =null, $header = null, $filename = "filename.xls"){
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Pragma: no-cache");
        header("Expires: 0");
        if(!$data) throw new Exception("Data is not null in printExcel");
        echo $header."\n".$data;
     }
     
     /**
      * 
      * @param type $data
      * @param type $filename
      * @author Bhavandeep Singh <bhavandeep@digimantra.com>
      * @uses For Genrate Csv File  
    */
     static function printCsv($data,  $filename){
        header("Content-type: text/x-csv");
        //header("Content-type: text/csv");
        //header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=".$filename);
        echo $data;
        exit;
     } 
     
     /**
      * 
      * @param type $str64
      * @return string
      * @throws Exception
      * @author Bhavandeep Singh <bhavandeep@digimantra.com>
      */
     static function getBase64Ext($str64){
        $data = explode(";",$str64);               
        if(!isset($data[0]))throw new Exception("String is not Base64 in common helper getBase64Ext");
        if(strtolower($data[0]) == "data:image/png") return "png"; 
        else return "jpg";
     }
     
     
    static function getBase64Image($str64){        
        return base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', str_replace(' ', '+',$str64)));        
     }
     
    static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    static function sendPush($deviceToken = null, $message, $title, $debug_environ = 'production', $option = false)
    {
        
        // Put your device token here (without spaces):
        $passphrase = '';
        $url = 'ssl://gateway.push.apple.com:2195';
        // setting up the environ incase there are typo errors
        if($debug_environ != 'developer') {
            $debug_environ = 'production';
        }
        $ctx = stream_context_create();
        if($debug_environ === 'developer'){
            $url = 'ssl://gateway.sandbox.push.apple.com:2195';
        }
        //stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__.'/development-Certificates.pem');
        stream_context_set_option($ctx, 'ssl', 'local_cert', __DIR__. "/{$debug_environ}.pem");
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        $fp = stream_socket_client($url, $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp) {
             exit("Failed to connect: $err $errstr" . PHP_EOL);
        }
        // Create the payload body
        $body['aps'] = array(
                'alert' => array(
                'body' => $message,
                'type' => $title,
                'custom' => (is_array($option) ? json_encode($option): '')
            ),
            'badge' => 0,
                'sound' => 'oven.caf',
                );
       
        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        return true;
        // Close the connection to the server
        fclose($fp);
    }
    
    static function sendPushAndroid($deviceToken = null, $message, $title,$option)
    {
        $note = Yii::$app->fcm->createNotification("Message", $message);
        $note->setIcon('notification_icon_resource_name')
                ->setColor('#ffffff')
                ->setBadge(1)
                ->setTag($option);
          $msg = Yii::$app->fcm->createMessage();
          $msg->addRecipient(new Device($deviceToken));
          $msg->setNotification($note);
          $response = Yii::$app->fcm->send($msg);
          $status = $response->getStatusCode();
    }

}
