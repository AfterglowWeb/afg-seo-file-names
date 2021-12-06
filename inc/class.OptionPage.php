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

        add_settings_field(
            'default_search_replace_options', 
            '', 
            array( $this, 'searchReplaceOptions' ),  // Callback
            'asf-settings',  // Page
            'default_settings_id'
        );

        add_settings_field(
            'default_search_replace', 
            '', 
            array( $this, 'searchReplaceField' ),  // Callback
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
        
        if( !isset($options['options']['is_paused']['value']) && $options['options']['is_paused']['value'] != '1' ) return;
        
        if(isset($this->_userOptions['is_paused'])) {

            $options['options']['is_paused']['value'] =  '1';
            $options['options']['is_paused']['checked'] = 'checked';

        } elseif(isset($this->_userOptions)) {

            $options['options']['is_paused']['value'] =  '0';
            $options['options']['is_paused']['checked'] = '';

        }

        $args = $options['options']['is_paused'];
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

        $value = false;
        if($this->_userOptions) {
            $value = isset($this->_userOptions['default_users']) && !empty($this->_userOptions['default_users']) ? $this->_userOptions['default_users'] : false; 
        } 

        ?>
        <div class="asf-field-wrapper asf-users">
            <p class="title"><b><?php echo esc_html(_n('Choose the user who will use SEO File Names','Choose the users who will use SEO File Names',$nUsers,'seo-file-names')); ?></b></p>
            <?php
            $i = 0;

            $asfUsers = $admins;
            $n = is_array($asfUsers) ? count($asfUsers) : 0;
            $subtitle = _n('Administrator','Administrators',$n,'seo-file-names');
            $args = $options['options']['default_users'];
            include realpath(AFG_ASF_PATH.'template-parts/field-users.php');

            $asfUsers = $editors;
            $n = is_array($asfUsers) ? count($asfUsers) : 0;
            $subtitle = _n('Editor','Editors',$n,'seo-file-names');
            $args = $options['options']['default_users'];
            include realpath(AFG_ASF_PATH.'template-parts/field-users.php');

            $asfUsers = $authors;
            $n = is_array($asfUsers) ? count($asfUsers) : 0;
            $subtitle = _n('Author','Authors',$n,'seo-file-names');
            $args = $options['options']['default_users'];
            include realpath(AFG_ASF_PATH.'template-parts/field-users.php');
            ?>
            <p class="notice"><?php echo esc_html(_n('Only selected user will run SEO File Names.','Only selected users will run SEO File Names.',$nUsers,'seo-file-names')); ?></p>
        </div>
    <?php }

    /**
    * Search Replace Fields Template
    */
    public function searchReplaceField() {
        
        $options = $this->_options->getOptions(); 
        
        $key = 'default_search_replace';

        if( !isset($options['options'][$key]) ) return;

        $args = $options['options'][$key];

        $pauseFieldConf = $args['rows'][0]['fields']['is_paused'];
        $searchFieldConf = $args['rows'][0]['fields']['search'];
        $replaceFieldConf = $args['rows'][0]['fields']['replace'];
        
        $values = false;
        if(isset($this->_userOptions[$key])) {
            $values = $this->_userOptions[$key];
        }

        

        $n = is_array($values) ? count($values) : 1;
        $i;

        for($i = 0; $i < $n; $i++) {
            
            $pauseFieldConf['args']['name'] = array($key,$i,'is_paused');
            $pauseFieldConf['args']['id'] = $i;
            $searchFieldConf['args']['name'] = array($key,$i,'search');
            $searchFieldConf['args']['id'] = $i;
            $replaceFieldConf['args']['name'] = array($key,$i,'replace');
            $replaceFieldConf['args']['id'] = $i;

            if( isset($values[$i]['is_paused']) ){
                
                $pauseFieldConf['args']['value'] = '1';
                $pauseFieldConf['args']['checked'] = 'checked';       

            } else {

                $pauseFieldConf['args']['value'] = '0';
                $pauseFieldConf['args']['checked'] = '';    

            }

            if( isset($values[$i]['search']) ) {
                
                $searchFieldConf['args']['value'] = sanitize_text_field($values[$i]['search']);

            }

            if( isset($values[$i]['replace']) ) {
                
                $replaceFieldConf['args']['value'] = $this->_sanitize->sanitizeString($values[$i]['replace'],true);

            }

            $args['rows'][$i]['fields']['is_paused'] = $pauseFieldConf;

            $args['rows'][$i]['fields']['search'] = $searchFieldConf;

            $args['rows'][$i]['fields']['replace'] = $replaceFieldConf;
            
        }

        include realpath(AFG_ASF_PATH.'template-parts/field-repeater.php');
    }

    public function searchReplaceOptions() {

        $options = $this->_options->getOptions(); 

        
        $key = 'default_search_replace_options';

        
        if( !isset($options['options'][$key]['fields']['filter_raw_filenames']) ) return;

        $args = $options['options'][$key]['fields']['filter_raw_filenames'];
        
        if(isset($this->_userOptions[$key]['fields']['filter_raw_filenames'])) {    
                $args['value'] = '1';
                $args['checked'] = 'checked';       

        } else {

                $args['value'] = '0';
                $args['checked'] = '';    

        } ?>
        <div class="asf-field-wrapper">
            <p class="title">
                <b><?php esc_html_e('Search and replace rules options','seo-file-names'); ?></b>
            </p>
            <?php include realpath(AFG_ASF_PATH.'template-parts/field-checkbox.php'); ?>
        </div>
    <?php }

}//END CLASS


if( is_admin() ) 
    $asf_optionPage = new asf_optionPage();