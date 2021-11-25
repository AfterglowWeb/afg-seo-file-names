<?php defined('ABSPATH') || exit; 

$name = $this->_sanitize->sanitizeInputName($args['name']);
$wrapper = isset($args['wrapper']) && in_array($args['wrapper'], array('div','li')) ? $args['wrapper'] : 'div'; 
$inputId = 'asf-'.esc_attr($name).'---'.esc_attr($args['id']);
$allowedTags = array(
    'b',
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array(),
    ),
);

$yes = isset($args['label-true']) && !$this->_sanitize->isEmpty($args['label-true']) ? $args['label-true'] : __('Yes','seo-file-names');
$no = isset($args['label-false']) && !$this->_sanitize->isEmpty($args['label-false']) ? $args['label-false'] : __('No','seo-file-names');

?>
<<?php echo esc_attr($wrapper); ?> class="asf-field-wrapper asf-checkbox <?php echo esc_attr($args['class']); ?>">
    <p>
        <span class="choice"><?php echo esc_html($no); ?></span>
        <label class="switch <?php echo esc_attr($args['switch-class']); ?>" for="<?php echo esc_attr($inputId); ?>">
            <input type="checkbox" id="<?php echo esc_attr($inputId); ?>" pattern="<?php echo esc_attr($args['pattern']);?>" value="<?php echo esc_attr($args['value']); ?>" name="<?php echo esc_attr($name); ?>" <?php echo esc_attr($args['checked']); ?> />
            <span class="slider round"></span>
         </label>
         <span class="choice"><?php echo esc_html($yes); ?></span>
     </p>
     <?php if(!empty($args['info-1'])) { ?>
        <p class="info-1"><?php echo wp_kses($args['info-1'],$allowedTags); ?></p>
    <?php } ?>
     <label for="<?php echo esc_attr($inputId); ?>">
        <span class="bold">
            <?php if(!empty($args['label'])) { ?>
                <span class="label"><?php echo esc_html($args['label']); ?></span>
            <?php } ?>
             <?php if(!empty($args['info-2'])) { ?>
                <span class="info-2"><?php echo wp_kses($args['info-2'],$allowedTags); ?></span>
            <?php } ?>
        </span>
        <?php if(!empty($args['info-3'])) { ?>
            <span class="info-3"><?php echo wp_kses($args['info-3'],$allowedTags); ?></span>
        <?php } ?>
    </label>
    <?php if(!empty($args['notice'])) { ?>
        <p class="notice"><?php echo wp_kses($args['notice'],$allowedTags); ?></p>
    <?php } ?>
</<?php echo esc_attr($wrapper); ?>>