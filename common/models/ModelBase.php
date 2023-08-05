<?php

namespace common\models;

use Yii;
use common\helpers\CommonHelper;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ModelBase extends \yii\db\ActiveRecord {
  public static $IS_ACTIVE = 1;
  public static $IN_ACTIVE = 0;
  public $eagerLoading = true;
  public $asArray = false;
  public $isSelected = false;
  /**
     *  Function to load default values like createddate, updateddate, createdby, updatedby
     * @return model current class object
     * 
    */     

  public function defaultUpdate() {
    CommonHelper::updateModelDefault($this);
    return $this;
  }

  public function save($runValidation = true, $attributeNames = NULL) {
    if ($this->isNewRecord) {
      $this->defaultUpdate();
    }
    if(!$this->validate()){
     $this->validateDefaults();
    }          
    return parent::save();
  }

  public function validateDefaults(){    
    $defaultArray = ['createdby', 'updatedby', 'createdby', 'createdby'];
    foreach($defaultArray as $key){
      if(array_key_exists($key , $this->getErrors())){
        $this->defaultUpdate();
      }
    }

  }

}
