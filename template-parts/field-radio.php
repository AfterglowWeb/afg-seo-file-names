<?php defined('ABSPATH') || exit; 

$name = $this->_sanitize->sanitizeInputName($args['name']);

$inputId = 'asf-'.esc_attr($name).'---'.esc_attr($args['value']); ?>
<li class="asf-field-wrapper asf-radio asf-flex-wrap <?php echo esc_attr($args['class']); ?>">
    <p>
        <span class="choice"><?php esc_html_e('No','seo-file-names'); ?></span>
        <label class="switch" for="<?php echo esc_attr($inputId); ?>">
            <input type="radio" id="<?php echo esc_attr($inputId); ?>" name="asf_options[<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($args['value']); ?>" <?php echo esc_attr($args['checked']);?> />
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
</li>