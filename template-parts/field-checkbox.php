<?php defined('ABSPATH') || exit; 
$inputId = 'asf-'.esc_attr($args['name']).'-'.esc_attr($args['value']); ?>
<div class="asf-field-wrapper asf-checkbox <?php echo esc_attr($args['class']); ?>">
    <p>
        <span class="choice"><?php esc_html_e('No','seo-file-names'); ?></span>
        <label class="switch" for="<?php echo esc_attr($inputId); ?>">
            <input type="checkbox" id="<?php echo esc_attr($inputId); ?>" value="<?php echo esc_attr($args['value']); ?>" name="asf_options[<?php echo esc_attr($args['name']); ?>][]" <?php echo esc_attr($args['checked']); ?> />
            <span class="slider round"></span>
         </label>
         <span class="choice"><?php esc_html_e('Yes','seo-file-names'); ?></span>
     </p>
     <label for="<?php echo esc_attr($inputId); ?>">
        <span class="bold">
            <?php if(!empty($args['label'])) { ?>
                <span><?php echo esc_html($args['label']); ?></span>
            <?php } ?>
            <?php if(!empty($args['info-1'])) { ?>
                <span><?php echo esc_html($args['info-1']); ?></span>
            <?php } ?>
        </span>
        <?php if(!empty($args['info-2'])) { ?>
            <span><?php echo esc_html($args['info-2']); ?></span>
        <?php } ?>
    </label>
    <?php if(!empty($args['notice'])) { ?>
        <p class="notice"><?php echo wp_kses($args['notice'],'b'); ?></p>
    <?php } ?>
</div>