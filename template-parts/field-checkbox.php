<?php defined('ABSPATH') || exit; ?>
<div class="asf-field-wrapper asf-flex-wrap">
    <b><?php echo $args['label']; ?></b>
    <span class="choice"><?php _e('No','asf'); ?></span>
    <label class="switch" for="asf-<?php echo $args['name']; ?>">
        <input type="checkbox" id="asf-<?php echo $args['name']; ?>" name="asf_options[<?php echo $args['name']; ?>]" <?php echo $args['value']; ?> />
        <span class="slider round"></span>
     </label>
     <span class="choice"><?php _e('Yes','asf'); ?></span>
    <p class="notice"><?php echo $args['info']; ?></p>
</div>