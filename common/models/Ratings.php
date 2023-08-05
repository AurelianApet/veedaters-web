<?php

  namespace common\models;

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
  class Ratings extends ModelBase 
  {

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
        [['like', 'dislike', 'avg_rating', 'item_id', 'relationship'], 'required']
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
        'avg_rating' => 'Average Rating',
        'item_id' => 'Item Id',
        'relationship' => 'Relationship'
      ];
    }

    

  }
  