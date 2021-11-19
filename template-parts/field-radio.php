<?php defined('ABSPATH') || exit; 
$inputId = 'asf-'.esc_attr($args['name']).'-'.esc_attr($args['value']); ?>
<li class="asf-field-wrapper asf-radio asf-flex-wrap">
     <label for="<?php echo esc_attr($inputId); ?>">
        <b>
            <?php if(!empty($args['label'])) { ?>
                <span><?php echo esc_html($args['label']); ?></span>
            <?php } ?>
            <?php if(!empty($args['info-1'])) { ?>
                <span><?php echo esc_html($args['info-1']); ?></span>
            <?php } ?>
        </b>
        <?php if(!empty($args['info-2'])) { ?>
            <p><?php echo esc_html($args['info-2']); ?></p>
        <?php } ?>
    </label>
    <p>
        <span class="choice"><?php esc_html_e('No','seo-file-names'); ?></span>
        <label class="switch" for="<?php echo esc_attr($inputId); ?>">
            <input type="radio" id="<?php echo esc_attr($inputId); ?>" name="asf_options[<?php echo esc_attr($args['name']); ?>]" value="<?php echo esc_attr($args['value']); ?>" <?php echo esc_attr($args['checked']);?> />
            <span class="slider round"></span>
         </label>
         <span class="choice"><?php esc_html_e('Yes','seo-file-names'); ?></span>
     </p>
     <?php if(!empty($args['notice'])) { ?>
        <p class="notice"><?php echo wp_kses($args['notice'],'b'); ?></p>
    <?php } ?>
</li>