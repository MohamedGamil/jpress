<?php
  $type = isset($type) && in_array( str_replace('PostLayout.', '', $type) , [ 'adMob', 'htmlAd', 'imageAd' ]) ? $type : 'adMob';
  $size = isset($size) && empty($size) === false ? $size : 'banner';
  $content = isset($content) && empty($content) === false ? $content : '';
  $image = isset($image) && empty($image) === false ? $image : '';
  $action = isset($action) && empty($action) === false ? $action : '';
  $target = isset($target) && empty($target) === false ? $target : '';
  $isAdmob = $type === 'adMob';
  $isHtml = $type === 'htmlAd';
  $isImage = $type === 'imageAd';
  $enabled = $isAdmob || $isHtml || $isImage;

  switch ($type) {
    case 'adMob':
      $enabled = empty(trim($size)) === false;
      break;
    case 'htmlAd':
      $enabled = empty(trim($content)) === false;
      break;
    case 'imageAd':
      $enabled = empty(trim($image)) === false && empty(trim($action)) === false && empty(trim($target)) === false;
      break;
  }
?>


<?php if ($enabled === true): ?>

<!-- AppBear Ad Block Start -->
<div>

  <?php if ($isAdmob === true): ?>
    <div class="adMob <?php echo $size; ?>"></div>
  <?php endif; ?>

  <?php if ($isHtml === true): ?>
    <div class="htmlAd">
      <?php echo $content . "\n"; ?>
    </div>
  <?php endif; ?>

  <?php if ($isImage === true): ?>
    <a class="imageAd" href="<?php echo $target; ?>" type="<?php echo $action; ?>">
      <img src="<?php echo $image; ?>"></a>
  <?php endif; ?>

</div>
<!-- / AppBear Ad Block End -->

<?php endif; ?>
