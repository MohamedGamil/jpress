<?php
  $message = isset($message) && $message ? $message : '';
  $type = isset($type) && $type ? $type : 'success';
  $dismissable = isset($dismissable) && $dismissable;
?>

<?php if (empty($message) === false) : ?>
  <div class="notice notice-<?php echo $type;?> <?php echo $dismissable ? 'dismissible is-dismissible' : ''; ?>">
    <p><?php echo $message;?></p>
  </div>
<?php endif; ?>
