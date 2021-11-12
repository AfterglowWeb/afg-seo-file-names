<?php defined('ABSPATH') || exit; 
$inputId = 'asf-'.esc_attr($args['name']); ?>
<div class="asf-field-wrapper asf-flex-wrap">
    <b><?php echo esc_html($args['label']); ?></b>
    <span class="choice"><?php esc_html_e('No','seo-file-names'); ?></span>
    <label class="switch" for="<?php echo esc_attr($inputId); ?>">
        <input type="checkbox" id="<?php echo esc_attr($inputId); ?>" name="asf_options[<?php echo esc_attr($args['name']); ?>]" <?php echo esc_attr($args['value']); ?> />
        <span class="slider round"></span>
     </label>
     <span class="choice"><?php esc_html_e('Yes','seo-file-names'); ?></span>
    <p class="notice"><?php echo wp_kses($args['info'],'b'); ?></p>
</div>