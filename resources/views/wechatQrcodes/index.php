<?php $view->layout() ?>

<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('plugins/wechat-qrcode/css/wechat-qrcodes.css') ?>">
<?= $block->end() ?>

<div class="qrcode-container">
  <div class="qrcode-header">
    欢迎关注我们
  </div>
  <p class="qrcode-tips">
    长按指纹识别二维码
  </p>
  <div class="qrcode-img-container">
    <img class="qrcode-img" src="<?= $image ?>">
    <img class="qrcode-cover-img" src="<?= $account['headImg'] ?>">
  </div>
  <?php $event->trigger('postWechatQrcodeRender', [$qrcode]) ?>
  <?= $setting('wechatQrcodes.description') ?>
</div>
