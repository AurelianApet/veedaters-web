<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . '/uploads');

require_once 'veedater-relationship.php';
require_once 'polyfills/function.array_column.php';
require_once 'polyfills/function.backtrace.php';
require_once 'polyfills/function.c.php';