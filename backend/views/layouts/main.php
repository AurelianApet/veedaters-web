<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */


if (Yii::$app->controller->action->id === 'login') { 
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    //$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    $directoryAsset = Yii::$app->urlManager->baseUrl;

    
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <?php
            $this->title = 'Veedater Admin';
        ?>
        <title><?= Html::encode($this->title) ?></title>
        
        <?= $this->registerCssFile('https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css') ?>
        <?php $this->head() ?>
        <link rel="apple-touch-icon" sizes="57x57" href="<?=$directoryAsset?>/images/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="<?=$directoryAsset?>/images/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="<?=$directoryAsset?>/images/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="<?=$directoryAsset?>/images/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="<?=$directoryAsset?>/images/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="<?=$directoryAsset?>/images/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="<?=$directoryAsset?>/images/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="<?=$directoryAsset?>/images/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?=$directoryAsset?>/images/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="<?=$directoryAsset?>/images/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="<?=$directoryAsset?>/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="<?=$directoryAsset?>/images/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?=$directoryAsset?>/images/favicon-16x16.png">
        <link rel="manifest" href="<?=$directoryAsset?>/images/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="<?=$directoryAsset?>/images/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">
    </head>
    <body class="hold-transition skin-blue sidebar-mini">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php $this->endBody() ?>
    <?= $this->registerJsFile('@web/js/custom.js') ?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <?= $this->registerJsFile('https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js') ?>
    
    <?= $this->registerJsFile('https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js') ?>
    
    <?= $this->registerJsFile('https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js') ?>
        
        
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
