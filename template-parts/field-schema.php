<?php defined('ABSPATH') || exit; ?>
<div class="asf-field-wrapper">
    <label class="notice" for="asf-default-schema">
        <?php _e('Click on a tag to start building the file name schema.','asf'); ?><br>
        <?php _e('You can insert arbitrary text between each tag.','asf'); ?>
    </label>
    <p class="asf-tags">
        <?php foreach ($options['tags'] as $key => $array) { ?>
            <span class="asf-tag">
                <button type="button" class="button button-secondary" aria-label="<?php echo wp_strip_all_tags($array['desc']); ?>" data-tag="<?php echo $key; ?>">
                    <?php echo $array['title']; ?>
                </button>
                <span class="after"></span>
                <span class="asf-modal"><?php echo $array['desc']; ?></span>
            </span>
        <?php } ?>
    </p>
    <p class="clear"></p>
    <input type="text" id="asf-default-schema" name="asf_options[default_schema]" value="<?php echo esc_attr($value);?>" placeholder="<?php echo $options['options']['default_schema']; ?>" />
    <button type="button" class="asf-clear button button-secondary" data-for="asf-default-schema"><?php _e('Clear','asf'); ?></button>
    <p class="notice">
        <?php _e('This filename scheme will apply to all files you upload to your website from now on.','asf'); ?>
    </p>
</div>