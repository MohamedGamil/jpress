<?php
  $message = isset($message) && $message ? $message : '';
  $type = isset($type) && $type ? $type : 'info';
  $dismissable = isset($dismissable) && $dismissable;
?>

<?php if (empty($message) === false) : ?>
  <div class="notice notice-<?php echo $type;?> <?php echo $dismissable ? 'is-dismissible' : ''; ?>">
    <p><?php echo $message;?></p>
  </div>
<?php endif; ?>
