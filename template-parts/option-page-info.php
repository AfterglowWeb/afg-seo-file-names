<?php defined('ABSPATH') || exit; ?>
<div class="asf-info asf-boxed">
    <h3 class="asf-section-header rocket"><?php echo esc_html(__('Goal of SEO File Names','asf')); ?></h3>
    <div class="asf-section-content">
        <ul class="p">
            <li><?php echo esc_html(__('SEO File Names aims to save you time and boost your SEO by automatically renaming the files you upload to the media library with SEO friendly names.','asf')); ?></li>
            <li><?php echo esc_html(__('This is often overlooked by website authors whereas search engines also rely on file names to understand and index them.','asf')); ?></li>
            <li><?php echo esc_html(__('It also happens to me very often that clients ask me to upload many images and documents to their website. This plugin has saved me a lot of time by removing the automatic renaming software step (and license fees) before importing the files.','asf')); ?></li>
        </ul>
    </div>
    <h3 class="asf-section-header idea"><?php echo esc_html(__('How does SEO File Names works?','asf')); ?></h3>
    <div class="asf-section-content">
        <ul class="p">
            <li><?php echo sprintf(esc_html(__('When you upload a file to the media library, SEO File Names gathers datas from the post, page or term you are currently editing: %stitle, slug, category, tag or taxonomy, type, dates, author...%s','asf')),'<b>','</b>'); ?></li>
            <li><?php echo sprintf(esc_html(__('...plus the global site datas: %ssite name and site description%s, useful to reinforce your brand.','asf')),'<b>','</b>'); ?></li>
            <li><?php echo esc_html(__('With this datas, you build the file names using the predefined tags.','asf')); ?></li>
            <li><?php echo esc_html(__('You can insert arbitrary text between each tag.','asf')); ?></li>
            <li><?php echo esc_html(__('The arbitrary text can only contain the characters [a-z], [0-9], space and \'-\'.','asf')); ?></li>
            <li><?php echo esc_html(__('Special characters, accented characters and capital letters will be filtered out.','asf')); ?></li>
            <li><?php echo esc_html(__('Each part of the file name will be separated from the others by dashes if they are not.','asf')); ?></li>
        </ul>
        <p class="notice inline notice-info"><?php echo esc_html(__('SEO File Names works best when adding files to the media library while editing articles, pages or terms, as it uses the datas being edited.','asf')); ?></p>
    </div>
</div>