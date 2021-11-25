<?php defined('ABSPATH') || exit; 

$name = $this->_sanitize->sanitizeInputName($args['name']);

$inputId = 'asf-'.esc_attr($name).'---'.esc_attr($args['id']); ?>
<div class="asf-field-wrapper <?php echo esc_attr($args['class']); ?>">
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
    <input type="<?php echo esc_attr($fieldType); ?>" id="<?php echo esc_attr($inputId); ?>" value="<?php echo esc_attr($args['value']); ?>" name="<?php echo esc_attr($name); ?>" placeholder="<?php echo esc_attr($args['placeholder']); ?>" data-id="<?php echo esc_attr($args['id']); ?>" />
    <?php if(!empty($args['notice'])) { ?>
        <p class="notice"><?php echo wp_kses($args['notice'],'b'); ?></p>
    <?php } ?>
</div>