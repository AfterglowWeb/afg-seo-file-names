<?php defined('ABSPATH') || exit; 
$inputId = 'asf-'.esc_attr($args['name']); ?>
<div class="asf-field-wrapper asf-flex-wrap">
    <b><?php echo esc_html($args['label']); ?></b>
    <span class="choice"><?php echo esc_html(__('No','asf')); ?></span>
    <label class="switch" for="<?php echo $inputId; ?>">
        <input type="checkbox" id="<?php echo $inputId; ?>" name="asf_options[<?php echo esc_attr($args['name']); ?>]" <?php echo esc_attr($args['value']); ?> />
        <span class="slider round"></span>
     </label>
     <span class="choice"><?php echo esc_html(__('Yes','asf')); ?></span>
    <p class="notice"><?php echo $args['info']; ?></p>
</div>