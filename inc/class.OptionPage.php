<?php defined( 'ABSPATH' ) || exit;

class asf_optionPage {

    private $_sanitize;
    private $_options;
    private $_userOptions;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'addOptionPage' ) );
        add_action( 'admin_init', array( $this, 'setOptionPage' ) );
        $this->_sanitize = new asf_Sanitize;
        $this->_options = new asf_options;
        $this->_userOptions = $this->_sanitize->sanitizeUserOptions(get_option('asf_options'));
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
            'asf_options_group', // Option group
            'asf_options', // Option name
            array(
                'sanitize_callback' => array($this->_sanitize,'sanitizeUserOptions'),
            )
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
            'users_settings_id', // ID
            esc_html(__('Choose users','seo-file-names')), // Title
            '', // Callback
            'asf-settings' // Page
        );

        add_settings_field(
            'default_users', 
            '', 
            array( $this, 'usersField' ),  // Callback
            'asf-settings',  // Page
            'users_settings_id'
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
                settings_fields( 'asf_options_group' );
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
        $options = $this->_options->getOptions(); 
        if( !isset($options['tags']) && !is_array($options['tags']) ) return;
        
        $value = $this->_userOptions && isset($this->_userOptions['default_schema']) ? $this->_userOptions['default_schema'] : '';

        $placeHolder = isset($options['options']['default_schema']) ? $this->_sanitize->sanitizeSchema($options['options']['default_schema']) : '';
        
        include realpath(AFG_ASF_PATH.'template-parts/field-schema.php');
    }

    /**
    * Pause Field Template
    */
    public function pauseField() {
        $options = $this->_options->getOptions(); 
        if( !isset($options['options']['is_paused']) && $options['options']['is_paused'] != '1' ) return;
        
        $value = '1';
        $checked = 'checked';
        
        if($this->_userOptions) {
           if( isset($this->_userOptions['is_paused']) && !empty($this->_userOptions['is_paused']) ) {
                $value = '1';
                $checked = 'checked'; 
            } else {
                $value = '0';
                $checked = ''; 
            }
        } 

        $args = array(
            'name'  => 'is_paused',
            'label' => esc_html(__('Do you want to pause the plugin ?','seo-file-names')),
            'value' => $value,
            'checked' => $checked,
            'notice'  => esc_html(__('If the plugin is active (not paused) and no file names schema is set, the following scheme will apply: ','seo-file-names')).'<b>'.$this->_sanitize->sanitizeSchema($options['options']['default_schema']).'</b>',
            'class' => 'single-checkbox',
        );
        include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php');
    }

    /**
    * UsersField Template
    */
    public function usersField() {
        $options = $options = $this->_options->getOptions(); 
        if( !isset($options['options']['default_users']) ) return;

        $admins = get_users( array( 'fields' => 'role', 'role' => 'administrator' ) );
        $admins = $this->_sanitize->sanitizeIds($admins);

        $editors = get_users( array( 'fields' => 'role', 'role' => 'editor' ) );
        $editors = $this->_sanitize->sanitizeIds($editors);

        $authors = get_users( array( 'fields' => 'role', 'role' => 'author' ) );
        $authors = $this->_sanitize->sanitizeIds($authors);
        
        $nUsers = 0;
        if($admins) $nUsers += count($admins);
        if($editors) $nUsers += count($editors);
        if($authors) $nUsers += count($authors);

        //Only admins can set this field
        if(!$admins) return false;
        if(!in_array(get_current_user_id(), $admins)) return false;

        $value = false;
        if($this->_userOptions) {
            $value = isset($this->_userOptions['default_users']) && !empty($this->_userOptions['default_users']) ? $this->_userOptions['default_users'] : false; 
        } ?>
        
        <p class="title"><b><?php echo esc_html(_n('Choose the user who will use SEO File Names:','Choose the users who will use SEO File Names:',$nUsers,'seo-file-names')); ?></b></p>
        <?php 
        $asfUsers = $admins;
        $subtitle = _n('Administrator','Administrators',count($asfUsers),'seo-file-names');
        include realpath(AFG_ASF_PATH.'template-parts/field-users.php');

        $asfUsers = $editors;
        $subtitle = _n('Editor','Editors',count($asfUsers),'seo-file-names');
        include realpath(AFG_ASF_PATH.'template-parts/field-users.php');

        $asfUsers = $authors;
        $subtitle = _n('Author','Authors',count($asfUsers),'seo-file-names');
        include realpath(AFG_ASF_PATH.'template-parts/field-users.php');
        ?>
        <div class="asf-notice-wrapper">
            <p class="notice"><b><?php echo esc_html(_n('Only selected user will run SEO File Names.','Only selected users will run SEO File Names.',$nUsers,'seo-file-names')); ?></b></p>
        </div>
    <?php }

}//END CLASS


if( is_admin() ) 
    $asf_optionPage = new asf_optionPage();