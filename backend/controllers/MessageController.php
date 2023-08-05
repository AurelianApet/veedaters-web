<?php

namespace backend\controllers;

use Yii;
use common\models\Message;
use common\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\MessageRecipient;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionList($user_id = null) {
        $user_id = Yii::$app->getRequest()->getQueryParam('id');
            $exclude = [];  
            $blockedUsers = BlockList::find()->alias('bl')
            ->addSelect('blocked_id')
            ->andWhere(['blocked_by_id' => $user_id, 'is_blocked' => REL_USER_BLOCK])
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
                        ['mR.recipient_id'=>$user_id],
                        ['mR.createdby'=>$user_id]
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
                if ($value['clear_for_recipient']==$user_id) {
                $value['message_body']="";
                // $value['info']=null;
                $value['messagePhoto']=null;
                }
                if ($value['delete_for']!=$user_id) {
                $messageList[]= $value;
                }
            }
            }
        // c($messageList); die;
        return $this->render('view', [
            'list' => $messageList,
        ]); 
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   if($id)
            {
                return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
        else{
            return $this->render('view');   
        }
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    // create message
    public function actionCreate() {     
        if (Yii::$app->request->isPost) {                           
              $message =  new Message();
              $message->message_subject = Yii::$app->request->post()['Message']['message_subject']; 
              $message->message_creator_id = Yii::$app->user->getId(); 
              $message->message_body = Yii::$app->request->post()['Message']['message_body']; 
              $message->message_parent_id = 0; 
              $message->message_type = Yii::$app->request->post()['Message']['message_type']; 
              $message->validate();
              $message->save();
              
              $message_recipient = new MessageRecipient();
              $message_recipient->message_id = $message->message_id;
              $message_recipient->recipient_id = Yii::$app->request->post()['Message']['recipient_id'];
              $message_recipient->is_read = Message::$IS_UNREAD;
              $message_recipient->save();              
            
        }
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->message_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(Url::to(['message/index']));
    }
    
    
    // search user
    public function actionSearchUser()
    {
        if(Yii::$app->request->post()['username'])
        {
            $model = \common\models\User::find()
                    ->andWhere(['like','username',Yii::$app->request->post()['username'].'%',false])
                    ->all(); 
            if(!empty($model))
            {
                echo "<ul id=user-list>";
                foreach ($model as $key => $value) {                    
                    echo "<li onClick=selectUser('". str_replace(' ','-', $value->username)."',".$value->id.")>".$value->username."</li>";
                }
                echo "</ul>";
            }
            else
            {
                echo "<ul id=user-list>No user found";
                echo "</ul>";
            }
        }
        
    }
    
    
    // send message
    public function actionSendMessage()
    {
        
        if(Yii::$app->request->post()['user_id'])
        {
            $message = new Message();
            $message->message_subject = Yii::$app->request->post()['message'];
            $message->message_body = Yii::$app->request->post()['message'];
            $message->message_parent_id = 0;
            $message->message_creator_id = \Yii::$app->user->getId();
            $message->validate();
            $message->save();
            
             $allmessages = Message::find()
             ->joinWith(['info mR'],true,'LEFT JOIN')
             ->andWhere(['mR.first_message_key' => 1 ])
             ->andWhere(['or',
                    ['m.message_creator_id'=> Yii::$app->user->getId()]
//                   ['m.message_creator_id'=> Yii::$app->user->getId(), 'mR.recipient_id'=>Yii::$app->request->post()['user_id']],
                  //['m.message_creator_id'=>Yii::$app->request->post()['user_id'], 'mR.recipient_id'=>Yii::$app->user->getId()]
             
                     ])->asArray()->alias('m')->all();
             $option['sender_id'] = (string) Yii::$app->user->getId();
             $option['sender_username'] = Yii::$app->user->getIdentity()->username;
           
             foreach($allmessages as $msg) {
                $mrecipient = MessageRecipient::find()->andwhere(['message_recipient_id'=>$msg['info']['message_recipient_id']])->one();
                if(!empty($mrecipient)){
                  $mrecipient->first_message_key=0;
                  $mrecipient->save();
                }
             }
             
            $messageRecipient = new MessageRecipient();
            $messageRecipient->message_id = $message->message_id;
            $messageRecipient->recipient_id = Yii::$app->request->post()['user_id'];
            $messageRecipient->is_read = 0;
            $messageRecipient->first_message_key = 1;
            $messageRecipient->validate();
            $messageRecipient->save();
           
            $option['message_body'] = $message->message_body;
            $option['createddate'] = $message->createddate;
            $option['sender_id'] = (string) Yii::$app->user->getId();  
              
      $device_token = Notification::find()->andWhere(['user_id' => Yii::$app->request->post()['user_id']])->all();
           
      
      if(!empty($device_token)) {
                    foreach ($device_token as $key => $value) {
                        
                      // update notification badge count   
                      $messageRecipient = 0;  
                      $messageRecipient = MessageRecipient::find()
                              ->andWhere(['recipient_id' => Yii::$app->request->post()['user_id'],'is_read' => Message::$IS_UNREAD])
                              ->asArray()
                              ->all();  
                     
                    $badgeCount = Notification::find()
                              ->andWhere(['device_token' => $value->device_token,'user_id' => Yii::$app->request->post()['user_id']])->one();
                  
                    // $badgeCount->badge_count = 1 + count($messageRecipient);
                    $badgeCount->badge_count = 1 + $value->badge_count;
                    
                    
                      $badgeCount->save();
                      
                      
                      // get sender name 
                      $user = User::find()->andWhere(['id' => Yii::$app->user->getId()])->one();
                      $username = !empty($user->username) ? $user->email : '';                       
                    
                      $notification_message = $this->processMessage('message_recieved',['USERNAME' => $username ]);
                   
                      $this->sendPush($value->device_token,$value->device_type, $notification_message, $title = "chat", $option);                  
                     
                      
                    }
                }
               
            return json_encode(['msg' => 'Message sent']);
        }
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
