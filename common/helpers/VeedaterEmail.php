<?php

  namespace common\helpers;

  use Yii;

  class VeedaterEmail
  {

    static function send($to, $subject = "", $html = "", $from = "") {
      if (empty($from)) {
        $from = Yii::$app->params["adminEmail"];
      }

//      if ((bool) self::checkEmailSettings() == false) {
//        return true;
//      }

      $from = "admin@veedater.com";
      $attachment = false;
      if (is_array($html)) {
        $mailHtml = $html['html'];
        if (
          array_key_exists('attachments', $html) &&
          array_key_exists('content', $html['attachments']) &&
          array_key_exists('options', $html['attachments'])) {
          $attachment = $html['attachments']['content'];
          $options = $html['attachments']['options'];
        }
        unset($html['html']);
        if ((bool) getenv('ENABLE_SENDGRID')) {
          $sendGrid = Yii::$app->mailer;
          $message = $sendGrid->compose($mailHtml, $html);
          return $message->setFrom($from)
                  ->setTo($to)
                  ->setSubject($subject)
                  ->send($sendGrid);          
        } else {
          Yii::$app->mailer->useFileTransport = false;
          $message = Yii::$app->mailer->compose($mailHtml, $html)->setTo($to)->setSubject($subject)->setFrom($from);
          if ($attachment) {
            $message->attachContent($attachment, $options);
          }
          return $message->send();
        }
      }
      return Yii::$app->mailer->compose()->setTo($to)->setSubject($subject)->setFrom($from)->setHtmlBody($html)->send();
    }

    static function checkEmailSettings() {
      return \Yii::$app->params['enableMailServer'];
    }

  }
  