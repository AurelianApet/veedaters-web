<?php

  namespace common\helpers;

  use Imagine\Gd\Imagine;
  use Imagine\Image\Box;
  use Imagine\Image\Point;

  class DigiBtvImage
  {

      public static $imagebtv;
      public $extension = 'png';

      /**
       * Method to invoke the Imagine class
       * 
       * @param string $path path to the image file
       * 
       * @return class Image
       */
      static function factory()
      {
          return self::getImagebtv();
      }

      public static function getImagebtv()
      {
          if (!self::$imagebtv) {
              self::$imagebtv = new DigiBtvImage();
          }
          return self::$imagebtv;
      }


      public function resize($image_path, $width, $height)
      {
          $imagine = new Imagine();
          $imagine->open($image_path)
            ->resize(new Box($width, $height))
            ->show('png');
      }

      public function getResized($image_path, $width, $height){
        $imagine = new Imagine();
        $image = $imagine->open($image_path);
        
        $ext = pathinfo($image_path, \PATHINFO_EXTENSION);
        
        return $image->resize(new Box($width, $height));
            
      }

  }
  