<?php defined('ABSPATH') || exit; ?>
<div class="asf-field-wrapper">
    <label class="notice" for="asf-default-schema">
        <?php echo esc_html(__('Click on a tag to start building the file name schema.','asf')); ?><br>
        <?php echo esc_html(__('You can insert arbitrary text between each tag.','asf')); ?>
    </label>
    <p class="asf-tags">
        <?php foreach ($options['tags'] as $key => $array) { ?>
            <span class="asf-tag">
                <button type="button" class="button button-secondary" aria-label="<?php echo esc_attr(wp_strip_all_tags($array['desc'])); ?>" data-tag="<?php echo esc_attr($key); ?>">
                    <?php echo esc_html($array['title']); ?>
                </button>
                <span class="after"></span>
                <span class="asf-modal"><?php echo esc_html($array['desc']); ?></span>
            </span>
        <?php } ?>
    </p>
    <p class="clear"></p>
    <input type="text" id="asf-default-schema" name="asf_options[default_schema]" value="<?php echo esc_attr($value);?>" placeholder="<?php echo esc_html($placeHolder); ?>" />
    <button type="button" class="asf-clear button button-secondary" data-for="asf-default-schema"><?php echo esc_html(__('Clear','asf')); ?></button>
    <p class="notice">
        <?php echo esc_html(__('This filename scheme will apply to all files you upload to your website from now on.','asf')); ?>
    </p>
</div>