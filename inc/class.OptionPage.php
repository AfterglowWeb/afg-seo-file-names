<?php defined( 'ABSPATH' ) || exit;

class asf_optionPage {

    private $_options = array();

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'addOptionPage' ) );
        add_action( 'admin_init', array( $this, 'setOptionPage' ) );
    }

    public function addOptionPage() {
        add_options_page(
            __('Seo File Names','asf'), 
            __('Seo File Names','asf'),
            'manage_options', 
            'asf-settings', 
            array( $this, 'optionPageTemplate' )
        );
    }

    public function setOptionPage() {        
        register_setting(
            'asf_option_group', // Option group
            'asf_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'global_settings_id', // ID
            __('Pause the plugin?','asf'), // Title
            '', // Callback
            'asf-settings' // Page
        );

        add_settings_field(
            'is_paused', 
            __('Do you want to pause the plugin?','asf'), // Title
            array( $this, 'pauseField' ),  // Callback
            'asf-settings',  // Page
            'global_settings_id'
        );

        add_settings_section(
            'default_settings_id', // ID
            __('File names settings','asf'), // Title
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
            <h1>Seo File Names</h1>
            <p class="asf-subtitle" aria-label="<?php _e('Plugin translated title','asf'); ?>"><?php echo __('Seo File Names','asf').' — v.'.AFG_ASF_VERSION; ?></p>
            <?php include AFG_ASF_PATH.'template-parts/option-page-info.php'; ?>
            <form method="post" action="options.php" class="asf-boxed">
                <?php 
                settings_fields( 'asf_option_group' );
                do_settings_sections( 'asf-settings' );
                submit_button();
                ?>
            </form>
            <?php include AFG_ASF_PATH.'template-parts/option-page-support.php'; ?>
        </div>
    <?php }

    /**
    * Sanitize inputs
    */
    public function sanitize( $input ) {
        if( isset( $input['default_schema'] ) && !empty( trim($input['default_schema']) ) ) {
            $schema = strtolower($this->asf_sanitizeTextFields($input['default_schema']));
            $schema = str_replace(' ', '-', $schema);
            $schema = preg_replace("/[^a-z0-9\-%]/", "", $schema);
            $input['default_schema'] = $schema;
        }
        if( isset( $input['is_paused'] ) ) {
            $input['is_paused'] = '1';
        }
        return $input;
    }

    /**
    * Schema Field Template
    */
    public function schemaField() {
        $options = new asf_options;
        $options = $options->getOptions(); 
        if( !isset($options['tags']) && !is_array($options['tags']) ) return;
        
        $value = get_option('asf_options');
        $value = isset($value['default_schema']) ? $value['default_schema'] : ''; 
        include AFG_ASF_PATH.'template-parts/field-schema.php';
    }

    /**
    * Pause Field Template
    */
    public function pauseField() {
        $options = new asf_options;
        $options = $options->getOptions(); 
        if( !isset($options['options']['is_paused']) ) return;
        $checked = $options['options']['is_paused'] == '1' ? 'checked' : '';
        if($value = get_option('asf_options')) {
            $checked = isset($value['is_paused']) ? 'checked' : ''; 
        } 
        $args = array(
            'name'  => 'is_paused',
            'label' => __('Do you want to pause the plugin ?','asf'),
            'value' => $checked,
            'info'  => __('If the plugin is active (not paused) and no file names schema is set, the following scheme will apply: ','asf').'<b>%blogname%%blogdesc%%filename%</b>',
        );
        include AFG_ASF_PATH.'template-parts/field-checkbox.php';
    }

    /**
    * Variation of '_sanitize_text_fields' native WP function
    * https://developer.wordpress.org/reference/functions/_sanitize_text_fields/
    * Removed the hexadecimal filter
    */
    private function asf_sanitizeTextFields( $str, $keep_newlines = false ) {
        if ( is_object( $str ) || is_array( $str ) ) {
            return '';
        }
     
        $str = (string) $str;
     
        $filtered = wp_check_invalid_utf8( $str );
     
        if ( strpos( $filtered, '<' ) !== false ) {
            $filtered = wp_pre_kses_less_than( $filtered );
            // This will strip extra whitespace for us.
            $filtered = wp_strip_all_tags( $filtered, false );
     
            // Use HTML entities in a special case to make sure no later
            // newline stripping stage could lead to a functional tag.
            $filtered = str_replace( "<\n", "&lt;\n", $filtered );
        }
     
        if ( ! $keep_newlines ) {
            $filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
        }
        $filtered = trim( $filtered );
     
     
        return $filtered;
    }

}


if( is_admin() ) 
    $asf_optionPage = new asf_optionPage();