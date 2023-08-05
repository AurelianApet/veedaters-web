<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use backend\models\UserMeta;
use backend\models;
use common\models\Subscription;



/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ModelBase implements \yii\web\IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const ROLE_ADMIN = 1;
    public static $IS_ACTIVE = 1;
    public static $IN_ACTIVE = 0;
    


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        
      return [
        [['username', 'password_hash'], 'required'],
        [['createdby','is_active','social_id','updatedby'], 'integer'],        
        ['is_active', 'integer', 'max'=>2],
        [['password_hash'], 'safe'],
        [['username', 'password_reset_token','social_media_type','verificationcode', 'email'], 'string', 'max' => 100],
        [['password_hash', 'auth_key','name','address'], 'string', 'max' => 255],
        [['email','social_id'], 'unique']
      ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'is_active' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    { 
        return static::findOne(['username' => $username, 'is_active' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'is_active' => self::STATUS_ACTIVE,
        ]);
    }
    
    
    public function getVerificationCode($code = "") {
        if(empty($code))
        $code = rand(100000, 999999);
        $this->verificationcode = Yii::$app->security->generatePasswordHash($code);
        return $code;
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    public function getUserRole() {
      return $this->hasOne(UserRoleMap::className(), ['user_id' => 'id']);
          
    }
    public function getNotification() { 
        return  $this->hasOne(Notification::className(), ['user_id' => 'id']);
    }
    public function getUserPreferences(){
        return $this->hasOne(UserPreference::className(),['user_id'=>'id']);
    }
    
    public function getAdmin() {
      return $this->hasOne(UserRoleMap::className(), ['user_id' => 'id'])->andOnCondition(['user_role_id' => [REL_ROLE_USER]]);          
    }
    
    public function getUserVideo() {
      return $this->hasOne(Videos::className(), [ 'id' => 'video_id'])->viaTable(VideoMap::tableName(), ['user_id' => 'id'], function($query) {
          $query->alias('uVM');
        })->alias('uV');
    }
    
    public function getUserProfileVideo() {
      return $this->hasOne(Videos::className(), [ 'id' => 'video_id'])->viaTable(VideoMap::tableName(), ['user_id' => 'id'], function($query) {
          $query->alias('uVM');
        })->alias('uV');
    }
    
    public function getUserVideoThumb() {
      return $this->hasOne(Photos::className(), [ 'photos_id' => 'photos_id'])->viaTable(PhotosMap::tableName(), ['item_id' => 'id'], function($query) {
          $query->alias('uVT');
          $query->andOnCondition(['uVT.relationship' => REL_VIDEO_THUMB]);
        });
    }
    
    
    
    public function getUserMeta() {
      return $this->hasMany(UserMeta::className(), ['user_id' => 'id'])->alias('uM')->addSelect(['uM.user_id','uM.meta_key','uM.meta_value'])->andOnCondition(['uM.meta_key' => [REL_USER_DOB,REL_USER_GENDER]]);          
    }
    
    public function getUserMetaAll() {
      return $this->hasMany(UserMeta::className(), ['user_id' => 'id'])->alias('uM')->addSelect(['uM.user_id','uM.meta_key','uM.meta_value'])->andOnCondition(['uM.meta_key' => User::userMetaKeys()])->andWhere(['NOT IN' ,'uM.meta_key',[REL_USER_LAT,REL_USER_LNG]]);          
    }
    
    public function getUserAge() {
      return $this->hasOne(UserMeta::className(), ['user_id' => 'id'])->alias('uM')->addSelect(['meta_value','meta_key','user_id','TIMESTAMPDIFF(YEAR, meta_value, CURDATE()) AS age'])->andOnCondition(['uM.meta_key' => [REL_USER_DOB]]);          
    }
    
    public function getUserAgeValue() {
         return $this->hasOne(UserMeta::className(), ['user_id' => 'id'])->alias('uM')->andOnCondition(['uM.meta_key' => [REL_USER_DOB]]);          
    }
    
    
    public function getUserGender() {
      return $this->hasOne(UserMeta::className(), ['user_id' => 'id'])->alias('uMG')->addSelect(['meta_value','meta_key','user_id'])->andOnCondition(['uMG.meta_key' => [REL_USER_GENDER]]);          
    }
    
    public function getPlan() {
      return $this->hasOne(Subscription::className(), ['user_id' => 'id']);          
    }
        
    public function getUserLike() {
      return $this->hasOne(Ratings::className(), ['user_id' => 'id'])->alias('uL')->addSelect(['uL.user_id','uL.review as like']);
    }
    
    public function getUserLikeStatus() {
      return $this->hasOne(Ratings::className(), ['user_id' => 'id'])->alias('uL')->addSelect(['uL.user_id','uL.createdby','uL.review as like']);
    }
    
    public function getUserBlock() {
      return $this->hasMany(Blocklist::className(), ['blocked_by_id' => 'id'])->alias('uB');          
    }
        
    
    public function getUserPhoto(){
      return $this->hasMany(Photos::className(), [ 'photos_id' => 'photos_id'])->viaTable(PhotosMap::tableName(), ['item_id' => 'id'], function($query) {
          $query->alias('uP');
          $query->andOnCondition(['uP.relationship' => REL_USER_PROFILE]);
        });
    }
          
    public function isAdmin(){
      return (bool) UserRoleMap::findOne(['user_id' => $this->id, 'user_role_id' => self::ROLE_ADMIN]);
    }

    public function getRuserPhoto(){
        return $this->hasOne(Photos::className(), [ 'photos_id' => 'photos_id'])->viaTable(PhotosMap::tableName(), ['item_id' => 'id'], function($query) {
            $query->alias('uP');
            $query->andOnCondition(['uP.relationship' => REL_USER_PROFILE]);
          });
    }
      
    public function getSuserPhoto(){
      return $this->hasOne(Photos::className(), [ 'photos_id' => 'photos_id'])->viaTable(PhotosMap::tableName(), ['item_id' => 'id'], function($query) {
          $query->alias('pic');
          $query->andOnCondition(['pic.relationship' => REL_USER_PROFILE]);
        })->alias('picp');
    }
    
    public static function loadUserData($user_id = false){
        if(!$user_id) return false;
        $user = [];

        $userMetaAbout = '';        
        $userMetaAbout = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_ABOUT]])->one();                      
        
        $userMetaAlchohol = '';        
        $userMetaAlchohol = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_ALCHOHOL]])->one();                      
        
        $userMetaDob = '';        
        $userMetaDob = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->addSelect('TIMESTAMPDIFF(YEAR, meta_value, CURDATE()) AS age')->andWhere(['user_id' => $user_id, 'meta_key' => [REL_USER_DOB]])->asArray()->one();
        
        $userMetaGender = '';        
        $userMetaGender = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_GENDER]])->one();
        
        $userMetaMinIncome = '';        
        $userMetaMinIncome = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_MIN_INCOME]])->one();
        
        $userMetaMaxIncome = '';        
        $userMetaMaxIncome = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_MAX_INCOME]])->one();
        
        $userMetaNation = '';        
        $userMetaNation = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_NATION]])->one();
        
        $userMetaReligion = '';        
        $userMetaReligion = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_RELIGION]])->one();
        
        $userMetaSmoke = '';        
        $userMetaSmoke = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_SMOKE]])->one();
        
        $userMetaSport = '';        
        $userMetaSport = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_SPORT]])->one();
        
        $userMetaStatus = '';        
        $userMetaStatus = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_STATUS]])->one();
        
        $userMetaStyle = '';        
        $userMetaStyle = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_STYLE]])->one();
        
        $userMetaTatto = '';        
        $userMetaTatto = UserMeta::find()->addSelect(['meta_key','meta_value','user_id'])->andWhere(['user_id' => $user_id,'meta_key' => [REL_USER_TATOO]])->one();
        
        $userLike = '';        
        $userLike = Ratings::find()->andWhere(['user_id' => $user_id,'createdby' => Yii::$app->user->getId()])->one();
        $user['user_meta']['about'] = !empty($userMetaAbout->meta_value) ? $userMetaAbout->meta_value: null;
        $user['user_meta']['alchohol'] = !empty($userMetaAlchohol->meta_value) ? $userMetaAlchohol->meta_value: null;
        $user['user_meta']['dob'] = !empty($userMetaDob['meta_value']) ? $userMetaDob['meta_value']: null;
        $user['user_meta']['age'] = !empty($userMetaDob['age']) ? $userMetaDob['age']: null;
        $user['user_meta']['gender'] = null;
        
        
        if(!empty($userMetaGender->meta_value) && $userMetaGender->meta_value == 'Women')
        {
            $user['user_meta']['gender'] = !empty($userMetaGender->meta_value) ? 'Woman': null;
        }
         
        if(!empty($userMetaGender->meta_value) && $userMetaGender->meta_value == 'Men')
        {
            $user['user_meta']['gender'] = !empty($userMetaGender->meta_value) ? 'Man': null;
        }
        
        if(!empty($userMetaGender->meta_value) && $userMetaGender->meta_value == 'Not specified')
        {
            $user['user_meta']['gender'] = !empty($userMetaGender->meta_value) ? 'Not specified': null;
        }
        
        
        $user['user_meta']['max_income'] = !empty($userMetaMaxIncome->meta_value) ? $userMetaMaxIncome->meta_value: null;
        $user['user_meta']['min_income'] = !empty($userMetaMinIncome->meta_value) ? $userMetaMinIncome->meta_value: null;
        $user['user_meta']['nation'] = !empty($userMetaNation->meta_value) ? $userMetaNation->meta_value: null;
        $user['user_meta']['religion'] = !empty($userMetaReligion->meta_value) ? $userMetaReligion->meta_value: null;
        $user['user_meta']['smoke'] = !empty($userMetaSmoke->meta_value) ? $userMetaSmoke->meta_value: null;
        $user['user_meta']['sport'] = !empty($userMetaSport->meta_value) ? $userMetaSport->meta_value: null;
        $user['user_meta']['status'] = !empty($userMetaStatus->meta_value) ? $userMetaStatus->meta_value: null;
        $user['user_meta']['style'] = !empty($userMetaStyle->meta_value) ? $userMetaStyle->meta_value: null;
        $user['user_meta']['tatoo'] = !empty($userMetaTatto->meta_value) ? $userMetaTatto->meta_value: null;      
        $user['user_meta']['like'] = !empty($userLike->review) ? $userLike->review: 0;      

        return $user;
    }

    public static function userMetaKeys(){
        return [
            REL_USER_ABOUT,
            REL_USER_DOB,
            REL_USER_STATUS,
            REL_USER_GENDER,
            REL_USER_RELIGION,
            REL_USER_NATION,
            REL_USER_SPORT,
            REL_USER_TATOO,
            REL_USER_MIN_INCOME,
            REL_USER_MAX_INCOME,
            REL_USER_STYLE,
            REL_USER_SMOKE,
            REL_USER_ALCHOHOL,
            REL_USER_LAT,
            REL_USER_LNG
        ];
    }
}
