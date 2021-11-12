<?php defined( 'ABSPATH' ) || exit;

class asf_optionPage {

    private $_options = array();
    private $_sanitize;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'addOptionPage' ) );
        add_action( 'admin_init', array( $this, 'setOptionPage' ) );
        $this->_sanitize = new asf_Sanitize;
    }

    public function addOptionPage() {
        add_options_page(
            esc_html(__('SEO File Names','seo-file-names')), 
            esc_html(__('SEO File Names','seo-file-names')),
            'manage_options', 
            'asf-settings', 
            array( $this, 'optionPageTemplate' )
        );
    }

    public function setOptionPage() {  
      
        register_setting(
            'asf_option_group', // Option group
            'asf_options', // Option name
            array($this->_sanitize,'sanitize'), // Sanitize
        );

        add_settings_section(
            'global_settings_id', // ID
            esc_html(__('Pause the plugin?','seo-file-names')), // Title
            '', // Callback
            'asf-settings' // Page
        );

        add_settings_field(
            'is_paused', 
            esc_html(__('Do you want to pause the plugin?','seo-file-names')), // Title
            array( $this, 'pauseField' ),  // Callback
            'asf-settings',  // Page
            'global_settings_id'
        );

        add_settings_section(
            'default_settings_id', // ID
            esc_html(__('File names settings','seo-file-names')), // Title
            '', // Callback
            'asf-settings' // Page
        );      

        add_settings_field(
            'default_schema', 
            '', 
            array( $this, 'schemaField' ),  // Callback
            'asf-settings',  // Page
            'default_settings_id'
        );      
    }

    /**
    * Render Option Page
    */
    public function optionPageTemplate() { ?>
        <div class="wrap">
            <h1>SEO File Names</h1>
            <p class="asf-subtitle" aria-label="<?php echo esc_attr(__('Plugin translated title','seo-file-names')); ?>">
                <?php echo esc_html(__('SEO File Names','seo-file-names').' — v.'.AFG_ASF_VERSION); ?>
             </p>
            <?php include realpath(AFG_ASF_PATH.'template-parts/option-page-info.php'); ?>
            <form method="post" action="options.php" class="asf-boxed">
                <?php 
                settings_fields( 'asf_option_group' );
                do_settings_sections( 'asf-settings' );
                submit_button();
                ?>
            </form>
            <?php include realpath(AFG_ASF_PATH.'template-parts/option-page-support.php'); ?>
        </div>
    <?php }


    /**
    * Schema Field Template
    */
    public function schemaField() {
        $options = new asf_options;
        $options = $options->getOptions(); 
        if( !isset($options['tags']) && !is_array($options['tags']) ) return;
        
        $userOptions = get_option('asf_options');
        $userOptions = $this->_sanitize->sanitizeUserOptions($userOptions,$options);
        $value = $userOptions && isset($userOptions['default_schema']) ? $userOptions['default_schema'] : '';

        $placeHolder = isset($options['options']['default_schema']) ? $this->_sanitize->sanitizeSchema($options['options']['default_schema']) : '';
        
        include realpath(AFG_ASF_PATH.'template-parts/field-schema.php');
    }

    /**
    * Pause Field Template
    */
    public function pauseField() {
        $options = new asf_options;
        $options = $options->getOptions(); 
        if( !isset($options['options']['is_paused']) ) return;
        $checked = $options['options']['is_paused'] == '1' ? 'checked' : '';
        
        $userOptions = get_option('asf_options');
        $userOptions = $this->_sanitize->sanitizeUserOptions($userOptions,$options);
        if($userOptions) {
            $checked = isset($userOptions['is_paused']) && $userOptions['is_paused'] == '1' ? 'checked' : ''; 
        } 

        $args = array(
            'name'  => 'is_paused',
            'label' => esc_html(__('Do you want to pause the plugin ?','seo-file-names')),
            'value' => $checked,
            'info'  => esc_html(__('If the plugin is active (not paused) and no file names schema is set, the following scheme will apply: ','seo-file-names')).'<b>'.$this->_sanitize->sanitizeSchema($options['options']['default_schema']).'</b>',
        );
        include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php');
    }



}


if( is_admin() ) 
    $asf_optionPage = new asf_optionPage();