<?php 

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace api\modules\v1\controllers;
use \common\models\User;
use \common\helpers;
use backend\models\UserMeta;
use \common\helpers\AssestsManager;
use common\models\PhotosMap;
use common\models\Photos;
use common\models\Message;
use \common\models\MessageRecipient;
use \common\models\UserRoleMap;
use \common\models\Blocklist;
use \common\models\Notification;
use common\helpers\CommonHelper;
use paragraph1\phpFCM\Recipient\Device;
use Yii;
use yii\helpers\Url;
/**
 * Description of SiteController
 *
 * @author Gurcharan  Singh <Gurcharan.singh@digimantra.com>
 */
class MessageController extends ApiController{
    //put your code here
    
    public $debug_environ = 'developer';

    public function actionError() { 
        $exception = Yii::$app->errorHandler->exception;        
        return ['status' => $exception->statusCode, 'message' => $exception->getMessage()];
    }
    
//    public function actionList() { 
//        $connection = Yii::$app->getDb();        
//        $command = $connection->createCommand("
//                SELECT message.message_id,message.message_creator_id,message.message_creator_id,
//                message_recipient.message_id,message_recipient.recipient_id, message.message_body
//                FROM message 
//                LEFT JOIN message_recipient 
//                ON message.message_id=message_recipient.message_id 
//                WHERE message.message_creator_id=".Yii::$app->user->getId()."
//                OR message_recipient.recipient_id=".Yii::$app->user->getId()."");
//        return $this->success(['is_success' => true, 'message' => $command->queryAll()]);        
//    }
    
    public function actionList() { 
          $this->initHeader();
          $exclude = [];  
          $blockedUsers = BlockList::find()->alias('bl')
          ->addSelect('blocked_id')
          ->andWhere(['blocked_by_id' => Yii::$app->user->getId(), 'is_blocked' => REL_USER_BLOCK])
          ->asArray()->all();
          if (!empty($blockedUsers) || !$blockedUsers) {
            $exclude = array_merge($exclude, array_column($blockedUsers, 'blocked_id'));
          }

          $query = Message::find()
                   ->joinWith(['info mR' => function($m){
                        $m->joinWith(['reciever' => function($r){
                              $r->joinWith(['ruserPhoto e']);
                          }],true,'LEFT JOIN');
                        $m->joinWith(['sender' => function($r){
                              $r->joinWith(['suserPhoto']);
                          }],true,'LEFT JOIN');
                        $m->addSelect(['mR.message_recipient_id', 'mR.message_id','mR.recipient_id','mR.first_message_key','mR.createdby','mR.createddate', 'mR.is_read']);
                    }])
                  ->joinWith(['messagePhoto'],true,'LEFT JOIN') 
                  ->andWhere(['mR.first_message_key' => 1])
                  ->andWhere(['or',
                         ['mR.recipient_id'=>Yii::$app->user->getId()],
                         ['mR.createdby'=>Yii::$app->user->getId()]
                     ])
                  // ->andWhere(['<>','mR.recipient_id', $exclude])
                   ->addSelect(['m.message_id', 'm.message_subject', 'm.delete_for','m.message_creator_id', 'm.message_body', 'm.clear_for_recipient', 'm.clear_for_sender', 'm.createdby', 'm.createddate'])
                   ->alias('m')->orderBy(['m.createddate' => SORT_DESC]);
                   $allmessages = $query->asArray()->all();

          $messageList = []; 
          foreach ($allmessages  as $key => $value) {
            if(in_array($value['info']['recipient_id'], $exclude) || in_array($value['info']['createdby'], $exclude))
            {
              unset($allmessages[$key]);
            }
            else
            {
              if ($value['clear_for_recipient']==Yii::$app->user->getId()) {
                $value['message_body']="";
                // $value['info']=null;
                $value['messagePhoto']=null;
              }
              if ($value['delete_for']!=Yii::$app->user->getId()) {
                $messageList[]= $value;
              }
            }
          }
        // c($messageList); die;
        return $this->success(['is_success' => true, 'list' => $messageList]);        
    }
    
    function timeago($date) {
       $timestamp = strtotime($date); 
       
       $strTime = array("second", "minute", "hour", "day", "month", "year");
       $length = array("60","60","24","30","12","10");

       $currentTime = time();
       if($currentTime >= $timestamp) {
        $diff     = time()- $timestamp;
        for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
        $diff = $diff / $length[$i];
        }

        $diff = round($diff);
        return $diff . " " . $strTime[$i] . "(s) ago ";
       }
  }

  private function processMessage($msgKey = '', $array = []){
        $string = Yii::$app->params['message_templates'][$msgKey]['text'];
        foreach($array as $key => $value){
            $string = str_replace('{'.strtoupper($key).'}', $value, $string);
        }
        return $string;
  }

  private function sendPush($deviceToken, $message = "", $title = "", $option = false){ 
    return CommonHelper::sendPush($deviceToken, $message, $title, $this->debug_environ, $option);
  }
  
  public function  actionTest()
  {
      $deviceToken = "";
      $message = "";
      $title = "";
      $option = "";
      return CommonHelper::sendPushAndroid($deviceToken, $message, $title,$option);
  }

    // create message
  public function actionCreate() { 
       
        $this->initHeader(); 
        $option['photo_path'] = "";       
        if (Yii::$app->request->isPost) {
            if(!isset(Yii::$app->request->post()['Message']['recipient_id']) && empty(Yii::$app->request->post()['Message']['recipient_id'])) {
                 return $this->error(['error' => "message recipient_id is required"]);
              }              
            if(!isset(Yii::$app->request->post()['Message']['message_body']) && empty(Yii::$app->request->post()['Message']['message_body'])){
                 return $this->error(['error' => "message body is required"]);
              }
            if(isset(Yii::$app->request->post()['debug_environ'])){
                $this->debug_environ = Yii::$app->request->post()['debug_environ'];
              }
            if(!isset(Yii::$app->request->post()['Message']['message_parent_id']) && empty(Yii::$app->request->post()['Message']['message_parent_id'])){
                 return $this->error(['error' => "message message_parent_id is required"]);
              } 
            $allmessages = Message::find()
             ->joinWith(['info mR'],true,'LEFT JOIN')
             ->andWhere(['mR.first_message_key' => 1 ])
             ->andWhere(['or',
                   ['m.message_creator_id'=>Yii::$app->user->getId(), 'mR.recipient_id'=>Yii::$app->request->post()['Message']['recipient_id']],
                   ['m.message_creator_id'=>Yii::$app->request->post()['Message']['recipient_id'], 'mR.recipient_id'=>Yii::$app->user->getId()]
               ])
             ->asArray()
             ->alias('m')
             ->all();
             $option['sender_id'] = (string) Yii::$app->user->getId();
             $option['sender_username'] = Yii::$app->user->getIdentity()->username;
             

             foreach($allmessages as $msg)
             {
                $mrecipient = MessageRecipient::find()
                              ->andwhere([
                                'message_recipient_id'=>$msg['info']['message_recipient_id']])
                              ->one();
                
                if(!empty($mrecipient))
                {
                  $mrecipient->first_message_key=0;
                  $mrecipient->save();
                } 

             }
              $message =  new Message();
              $message->message_creator_id = Yii::$app->user->getId();
              if(isset(Yii::$app->request->post()['Message']['message_body']) && Yii::$app->request->post()['Message']['message_body']!="") {
                 $message->message_body = Yii::$app->request->post()['Message']['message_body'];
               }
              $imageFile = \yii\web\UploadedFile::getInstancesByName('Message[attachment]'); 
              if(empty($imageFile)){
                $message->message_body = Yii::$app->request->post()['Message']['message_body'];
                $message->message_subject = 'this is message subject';
              }
              else
              {
                if(!isset(Yii::$app->request->post()['Message']['message_body']) || Yii::$app->request->post()['Message']['message_body']=="")
                {
                  $message->message_body = "";  
                }
                else
                {
                  $message->message_body = Yii::$app->request->post()['Message']['message_body'];
                }
                
                $message->message_subject = ""; 
              }
              if(isset(Yii::$app->request->post()['Message']['message_subject']) && !empty(Yii::$app->request->post()['Message']['message_subject'])){
                $message->message_subject = Yii::$app->request->post()['Message']['message_subject'];
              }
              $message->message_parent_id = 0; 
              $message->is_active = 0; 
              $message->save();
              $option['message_body'] = $message->message_body;
              $option['createddate'] = $message->createddate;

           /******************* Image Code ***************************/
             $imageFile = \yii\web\UploadedFile::getInstancesByName('Message[attachment]');  
            if(!empty($imageFile))
            {  
                $imageFilesData = [];
                $imageFile = \yii\web\UploadedFile::getInstancesByName('Message[attachment]');
                if ($imageFile) { 
                  $formSingle = new \api\modules\v1\models\ImageUploadForm();
                  $formSingle->temp_images = $imageFile;
                  if ($formSingle->upload('_message')) { 
                    if(!file_exists(\Yii::getAlias('@uploads') . '/message/')) {
                        $old_umask = umask(0);                    
                        mkdir(\Yii::getAlias('@uploads') . '/message/', 0777, true);
                        chmod(\Yii::getAlias('@uploads') . '/message/', 0777);
                        umask($old_umask);
                    }
                    if(!file_exists(\Yii::getAlias('@uploads') . '/message/'.$message->message_id)) {
                        $old_umask = umask(0);
                        mkdir(\Yii::getAlias('@uploads') . '/message/'.$message->message_id, 0777, true);
                        umask($old_umask);
                    }
                    
                    $photo_map = '';
                    $photo_map = PhotosMap::find()->andWhere(['item_id' =>$message->message_id,'relationship' => REL_MESSAGE_PICTURE])->all();
                    $fileDateArray = $this->array_column($formSingle->response, 'savedName');
                    $photoUploader = new \common\helpers\PhotoUploader(AssestsManager::PHOTO_DIR_MESSAGE);                
                    $photoUploader->entity = $message;
                    $photoUploader->relationship = REL_MESSAGE_PICTURE;
                    $return = $photoUploader->upload($fileDateArray);
                    if(is_array($return)){                        
                      $option['photo_path'] = $return[0]->photo_path;
                    }
                    $photoUploader->upload($fileDateArray);
                    
                    $photo_map = PhotosMap::find()->andWhere(['item_id' =>$message->message_id])->one();
                    $photo = Photos::find()->andWhere(['photos_id' => $message->message_id])->one();
                  }
                }
            }
            /*******************End Image Code ***************************/
            /******************* Notification Code ***********************/
             $device_token = Notification::find()->andWhere(['user_id' => Yii::$app->request->post()['Message']['recipient_id']])->all();
             
             if(!empty($device_token)) {
                  foreach ($device_token as $key => $value) {
                    // get sender name 
                    
                    $user = User::find()->andWhere(['id' => Yii::$app->user->getId()])->one();
                    $username = !empty($user->username) ? $user->email : '';
                    $name = !empty($user->name) ? $user->name : '';
                    if($value->device_type=="iOS")
                    {
                      $notification_message = $this->processMessage('message_recieved',['USERNAME' => $username,'NAME' => $name ]);
                      $this->sendPush($value->device_token, $notification_message, $title = "chat", $option);  
                    } 
                    else
                    { 
                        $deviceToken = $value->device_token;
                        $notification_message = $this->processMessage('message_recieved',['USERNAME' => $username,'NAME' => $name  ]);
                        CommonHelper::sendPushAndroid($value->device_token,$notification_message,$title = "chat",$option);
                    }
                                      
                  }
              }
              

            /******************* End Notification Code *******************/
              
              $message_recipient = new MessageRecipient();
              $message_recipient->message_id = $message->message_id;
              $message_recipient->first_message_key=1;
              $message_recipient->recipient_id = Yii::$app->request->post()['Message']['recipient_id'];
              $message_recipient->is_read = Message::$IS_UNREAD;
              $message_recipient->save();              
            return $this->success(['is_success' => true, 'message' => 'message sent!']);        
        }
    }

    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
    /******************* Message Detail API ********************/

    public function actionDetail() {  
      $this->initHeader();    
      if (Yii::$app->request->isPost) { 
        if(!isset(Yii::$app->request->post()['Message']['user_id']) && empty(Yii::$app->request->post()['Message']['user_id']))
        {
           return $this->error(['error' => "message user_id is required"]);
        }       
        $allmessages = Message::find()
                    ->joinWith(['info mR' => function($m){
                          $m->joinWith(['reciever'],true,'LEFT JOIN');
                          $m->joinWith(['sender'],true,'LEFT JOIN');
                          $m->addSelect(['mR.message_recipient_id', 'mR.message_id','mR.recipient_id','mR.first_message_key','mR.createdby','mR.createddate', 'mR.is_read']);
                      }])
                     // ->andWhere(['mR.first_message_key' => 1, 'mR.recipient_id' => Yii::$app->user->getId()])
                    ->andWhere(['or',
                     ['m.message_creator_id'=>Yii::$app->user->getId(), 'mR.recipient_id'=>Yii::$app->request->post()['Message']['user_id']],
                     ['m.message_creator_id'=>Yii::$app->request->post()['Message']['user_id'], 'mR.recipient_id'=>Yii::$app->user->getId()]
                    ])
                   ->addSelect(['m.message_id', 'm.message_subject', 'm.message_creator_id', 'm.message_body', 'm.createdby', 'm.createddate', 'm.clear_for_recipient', 'm.clear_for_sender'])
                   ->asArray()->alias('m')->all();
          foreach ($allmessages as $message) {
            if($message['info']['recipient_id']==Yii::$app->user->getId())
            {
                $mrecipient = MessageRecipient::find()
                              ->andwhere([
                                'message_recipient_id'=>$message['info']['message_recipient_id']])
                              ->one();
                
                if(!empty($mrecipient))
                {
                  $mrecipient->is_read=1;
                  $mrecipient->save();
                } 
            }
          }

          /******************* After Update Read Status ****************/
          $messages = Message::find()
                    ->joinWith(['info mR' => function($m){
                          $m->joinWith(['reciever'],true,'LEFT JOIN');
                          $m->joinWith(['sender'],true,'LEFT JOIN');
                          $m->addSelect(['mR.message_recipient_id', 'mR.message_id','mR.recipient_id','mR.first_message_key','mR.createdby','mR.createddate', 'mR.is_read']);
                      }])
                    ->joinWith(['messagePhoto'],true,'LEFT JOIN') 
                    ->andWhere(['or',
                     ['m.message_creator_id'=>Yii::$app->user->getId(), 'mR.recipient_id'=>Yii::$app->request->post()['Message']['user_id']],
                     ['m.message_creator_id'=>Yii::$app->request->post()['Message']['user_id'], 'mR.recipient_id'=>Yii::$app->user->getId()]
                    ])
                    ->andWhere(['!=','m.clear_for_recipient',[Yii::$app->request->post()['Message']['user_id'],Yii::$app->user->getId()]])          
                    ->andWhere(['!=','m.clear_for_sender',[Yii::$app->request->post()['Message']['user_id'],Yii::$app->user->getId()]])          
                   
                   ->addSelect(['m.message_id', 'm.message_subject', 'm.message_creator_id', 'm.message_body', 'm.createdby', 'm.createddate', 'm.clear_for_recipient','m.clear_for_sender'])
                   ->orderBy(['m.message_id' => SORT_ASC])                   
                   ->asArray()->alias('m')
                    ->all();
                   //->createCommand()->rawSql;
                   //echo $messages->createCommand()->rawSql; 
                    //  die;              
                   
          /******************* End After Update Read Status ****************/
          
            $messageList = []; 
            foreach ($messages  as $key => $value) {
            if(!empty($value['messagePhoto']['photo_path']))
            {
                $value['messagePhoto']['photo_path_andriod'] = Url::to(Yii::$app->urlManagerBackend->baseUrl).AssestsManager::UPLOAD_PATH.$value['messagePhoto']['photo_path'];
            }
            if($value['clear_for_sender'] == Yii::$app->user->getId() && $value['clear_for_recipient'] == Yii::$app->request->post()['Message']['user_id'])
              { 
                unset($messages[$key]);
              }
              else
              {
                $messageList[]= $value;
              }
            }

          return $this->success(['is_success' => true, 'list' => $messageList]); 
        }
               
    }


    public function actionDeleteThread(){
        $this->initHeader();
        if (Yii::$app->request->isPost) {
            try{
            $withUser = Yii::$app->request->post('Message')['user_id'];
            $user = Yii::$app->user->getId();
            $messagesIds = MessageRecipient::find()->andWhere(['recipient_id' => $withUser,'createdby' => $user])
            ->orWhere(['recipient_id' => $user,'createdby' => $withUser])->addSelect(['message_id'])->asArray()->all();
            if(!empty($messagesIds)){
                $messagesIds = array_column($messagesIds, 'message_id');
                foreach($messagesIds as $id){
                    $model = Message::findOne($id);
                    $model->delete_for = Yii::$app->user->getId();
                    $model->save();
                }
            }
            // $return = MessageRecipient::deleteAll('(recipient_id = :recipient_id AND createdby = :createdby) OR (recipient_id = :createdby AND createdby = :recipient_id) ', [':recipient_id' => $withUser, ':createdby' => $user]);
            $return = count($messagesIds);
            return $this->success(['is_success' => true, 'message' => "Deleted {$return} messages"]);
            }catch(\Exception $e){
                return $this->error(['error' => $e->getMessage()]);
            }     
        }

    }



    public function actionClearchat(){
        $this->initHeader();
        if (Yii::$app->request->isPost) { 
          if(!isset(Yii::$app->request->post()['Message']['user_id']) && empty(Yii::$app->request->post()['Message']['user_id']))
          {
             return $this->error(['error' => "message user_id is required"]);
          }       
          $allmessages = Message::find()
                      ->joinWith(['info mR' => function($m){
                            $m->joinWith(['reciever'],true,'LEFT JOIN');
                            $m->joinWith(['sender'],true,'LEFT JOIN');
                            $m->addSelect(['mR.message_recipient_id', 'mR.message_id','mR.recipient_id','mR.first_message_key','mR.createdby','mR.createddate', 'mR.is_read']);
                        }])
                      ->andWhere(['or',
                       ['m.message_creator_id'=>Yii::$app->user->getId(), 'mR.recipient_id'=>Yii::$app->request->post()['Message']['user_id']],
                       ['m.message_creator_id'=>Yii::$app->request->post()['Message']['user_id'], 'mR.recipient_id'=>Yii::$app->user->getId()]
                      ])
                      ->andWhere(['or',
                       ['m.clear_for_sender'=>0],
                       ['m.clear_for_recipient'=>0]
                      ])          
   
                     ->addSelect(['m.message_id', 'm.message_subject', 'm.message_creator_id', 'm.message_body', 'm.createdby', 'm.createddate', 'm.clear_for_sender', 'm.clear_for_recipient'])
                     ->asArray()->alias('m')->all();
          
        }
        foreach($allmessages as $message)
        {
          $msg = Message::find()->where(['message_id' => $message['message_id']])->one();
          if($msg->message_creator_id == Yii::$app->request->post()['Message']['user_id'] )
          {
            $msg->clear_for_sender = Yii::$app->request->post()['Message']['user_id'];
          }
          else
          {
            $msg->clear_for_recipient = Yii::$app->request->post()['Message']['user_id'];
          }
          $msg->save();
        } 

        return $this->success(['is_success' => true]); 
      
    }
        
}
