<?php

  namespace backend\models;

  use Yii;

  /**
   * This is the model class for table "{{%level}}".
   *
   * @property integer $level_id
   * @property string $signup_date
   * @property integer $level_index
   * @property integer $ambassador_id
   *
   * @property CommisionMap $ambassador
   */
  class Ratings extends \common\models\ModelBase
  {
      
    public static $IS_ACTIVE = 1;
    public static $IN_ACTIVE = 0;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
      return '{{%ratings}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
      return [
        [['user_id','review'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
      return [
        'like' => 'Like',
        'dislike' => 'Dislike',
        'user_id' => 'User Id'
      ];
    }

    

  }
  