<?php defined('ABSPATH') || exit; 

$rows = is_array($args['rows']) ? $args['rows'] : false;

if(!$rows) return false;

$title = isset($args['title']) ? sanitize_text_field($args['title']) : false;
$subtitle = isset($args['subtitle']) ? sanitize_text_field($args['subtitle']) : false;
$notice = isset($args['notice']) ? sanitize_text_field($args['notice']) : false;
$class = isset($args['class']) ? sanitize_text_field($args['class']) : '';
$buttonText = isset($args['button-text']) ? sanitize_text_field($args['button-text']) : '';

?>
<div class="asf-field-wrapper asf-repeater <?php echo esc_attr($class); ?>">
    <?php if($title) { ?>
        <p class="title"><?php echo esc_html($title); ?></p>
    <?php } ?>
    <?php if($subtitle) { ?>
        <p class="subtitle"><?php echo esc_html($subtitle); ?></p>
    <?php } ?>
    <div class="asf-rows">
        <?php $r = 1; ?>
        <?php foreach ($rows as $row) {

            $row = is_array($row) ? $row : false;
            if(!$row) continue; 

            ?>
            <div class="asf-flex-wrap asf-row" data-id="<?php echo esc_attr($r); ?>">
            <?php foreach ($row as $key => $fields) {
                
                if($key !== 'fields') continue; 

                foreach ($fields as $field) {
                   
                    if(!isset($field['type']) || !isset($field['args'])) continue;

                    $args = $field['args'];
                    
                    $fieldType = sanitize_text_field($field['type']);

                    switch($fieldType) {
                        case 'text' :
                        case 'email' :
                        case 'number' :
                            include realpath(AFG_ASF_PATH.'template-parts/field-input.php');
                            break;

                        case 'text-filtered' :
                            $fieldType = 'text';
                            include realpath(AFG_ASF_PATH.'template-parts/field-input.php');
                            break;

                        case 'radio' :
                            include realpath(AFG_ASF_PATH.'template-parts/field-radio.php');
                            break;

                        case 'checkbox' :
                            include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php');
                            break;

                        case 'checkbox-boolean' :
                            $fieldType = 'checkbox';
                            include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php');
                            break;
                    }
                } ?>
                <div class="asf-flex-30">
                    <a href="#" rel="nofollow" data-event="remove-row" class="button asf-picto minus" title="<?php esc_attr_e('Remove row','seo-file-names'); ?>">-</a>
                    <a href="#" rel="nofollow" data-event="duplicate-row" class="button asf-picto duplicate" title="<?php esc_attr_e('Duplicate row','seo-file-names'); ?>">-</a>
                </div>
            <?php } ?>
            </div>
            <?php $r++; ?>
        <?php } ?>
    </div>
    <div class="asf-submit-wrapper"><button type="button" class="button" data-event="add-row"><?php echo esc_html($buttonText); ?></button></div>
    <?php if($notice) { ?>
        <p class="notice"><?php echo wp_kses($notice,'b'); ?></p>
    <?php } ?>
</div>