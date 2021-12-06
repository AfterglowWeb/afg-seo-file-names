<?php

defined( 'ABSPATH' ) || exit;

class asf_options {

    private $_options = array();

    public function __construct() {

        $this->setOptions();

    }

    public function getOptions() {

        return $this->_options;

    }

    private function setOptions() {

            $this->_options['options'] = array(
                'default_schema' => '%blogname%%blogdesc%%filename%',
                'is_paused' => array(
                    'id' => 1,
                    'name'  => 'is_paused',
                    'label' => esc_html(__('Do you want to pause the plugin?','seo-file-names')),
                    'label-true' => '',
                    'label-false' => '',
                    'value' => '1',
                    'info-1'  => '',
                    'info-2'  => '',
                    'info-3'  => '',
                    'checked' => 'checked',
                    'notice'  => wp_kses(__('If the plugin is active (not paused) and no file names schema is set, the following scheme will apply: <b>%blogname%%blogdesc%%filename%</b>','seo-file-names'),'b'),
                    'class' => 'single-checkbox',
                    'switch-class' => '',
                    'wrapper' => 'div',
                    'pattern' => '',
                ),
                'default_users' => array(
                    'id' => 2,
                    'name'  => array('default_users',''),
                    'label' => '',
                    'value' => '',
                    'info-1'  => '',
                    'info-2'  => esc_html(__(' %s(ID: %d) %s','seo-file-names')),
                    'info-3'  => '',
                    'checked' => '',
                    'notice'  => '',
                    'class' => 'small',
                    'switch-class' => 'small',
                    'wrapper' => 'li',
                    'pattern' => '',
                ),

                'default_search_replace_options' => array(
                    'fields' => array(
                                'filter_raw_filenames' => array(
                                    'id' => 3,
                                    'name'  => array('default_search_replace_options','fields','filter_raw_filenames'),
                                    'label' => esc_html(__('Apply rules on raw filenames (before schema is applied) instead of rewrited filenames (after schema is applied)?','seo-file-names')),
                                    'label-true' => esc_html(__('Apply rules on raw filename','seo-file-names')),
                                    'label-false' => esc_html(__('Apply rules on schema filtered filename','seo-file-names')),
                                    'value' => '0',
                                    'info-1'  => '',
                                    'info-2'  => '',
                                    'info-3'  => esc_html(__('If you apply rules on raw filenames, the results of your filters can be injected in the schema field with %replace-1%, %replace-2%... tags','seo-file-names')),
                                    'checked' => '',
                                    'notice'  => '',
                                    'class' => 'single-checkbox',
                                    'switch-class' => '',
                                    'wrapper' => 'div',
                                    'pattern' => '[0-1]{1}',
                                ),
                            ),
                ),

                'default_search_replace' => array(
                    'id' => 4,
                    'title' => esc_html(__('Search and replace rules','seo-file-names')),
                    'subtitle' => '',
                    'notice' => esc_html(__('You can add as many rules as you want.','seo-file-names')),
                    'class' => 'asf-search-replace',
                    'button-text' => esc_html(__('Add new rule','seo-file-names')),
                    'rows' => array(
                        0 => array(
                            'fields' => array(
                                'is_paused' => array(
                                    'type'=>'checkbox-boolean',
                                    'args'=> array(
                                        'id' => 1,
                                        'name'  => array('default_search_replace','0','is_paused'),
                                        'label' => esc_html(__('Pause?','seo-file-names')),
                                        'label-true' => '',
                                        'label-false' => '',
                                        'value' => '1',
                                        'info-1'  => esc_html(__('Temporarily disable this rule','seo-file-names')),
                                        'info-2'  => '',
                                        'info-3'  => '',
                                        'checked' => 'checked',
                                        'notice'  => '',
                                        'class' => 'small',
                                        'switch-class' => 'small',
                                        'wrapper' => 'div',
                                        'pattern' => '[0-1]{1}',
                                    ),
                                ),
                                'search' => array(
                                    'type'=>'text',
                                    'args'=> array(
                                        'id' => 2,
                                        'name'  => array('default_search_replace','0','search'),
                                        'label' => __('Search','seo-file-names'),
                                        'value' => '',
                                        'placeholder' => __('Type a string to search','seo-file-names'),
                                        'info-1'  => '',
                                        'info-2'  => '',
                                        'class' => 'asf-text',
                                        'pattern' => '',
                                    ),
                                ),
                                'replace' => array(
                                    'type'=>'text-filtered',
                                    'args'=> array(
                                        'id' => 3,
                                        'name'  => array('default_search_replace','0','replace'),
                                        'label' => __('Replace','seo-file-names'),
                                        'value' => '',
                                        'placeholder' => __('Type a string to replace your matches','seo-file-names'),
                                        'info-1'  => '',
                                        'info-2'  => '',
                                        'class' => 'asf-text',
                                        'pattern' => '',
                                    ),
                                ),
                            ),
                        ), 
                    ), 
                ),
            );

            $this->_options['tags'] = array(
                'title' => array(
                    'title'  => esc_html(__('Title','seo-file-names')),
                    'desc'   => esc_html(__('Current post, page or term title','seo-file-names')),     
                    'value'  => '',     
                ),
                'slug' => array(
                    'title'  => esc_html(__('Slug','seo-file-names')),
                    'desc'   => esc_html(__('Current post, page or term slug','seo-file-names')),  
                    'value'  => '',    
                ),
                'type' => array(
                    'title'  => esc_html(__('Type','seo-file-names')),
                    'desc'   => esc_html(__('Current post or page type. On terms, the type of post to which the term is linked','seo-file-names')),
                    'value'  => '',      
                ),
                'tag' => array(
                    'title'  => __('Tag','seo-file-names'),
                    'desc'   =>  __('Current post or page tag, empty on terms','seo-file-names'),
                    'value'  => '',      
                ),
                'cat' => array(
                    'title'  => __('Category','seo-file-names'),
                    'desc'   => __('Current post or page category, empty on terms','seo-file-names'),
                    'value'  => '',      
                ),
                'author' => array(
                    'title'  => __('Author','seo-file-names'),
                    'desc'   => __('Current post or page author, empty on terms','seo-file-names'),
                    'value'  => '',      
                ),
                'taxonomy'  => array(
                    'title'  => __('Taxonomy','seo-file-names'),
                    'desc'   => __('Current term taxonomy name, empty on posts and pages','seo-file-names'), 
                    'value'  => '',     
                ),
                'datepublished' => array(
                    'title'  => __('Date published','seo-file-names'),
                    'desc'   => __('Current post or page first published date, empty on terms','seo-file-names'),
                    'value'  => '',      
                ),
                'datemodified'  => array(
                    'title'  => __('Date modified','seo-file-names'),
                    'desc'   => __('Last date, current post or page as been modified, empty on terms','seo-file-names'),
                    'value'  => '',      
                ),
                'blogname' => array(
                    'title'  => __('Site name','seo-file-names'),
                    'desc'   => __('The site name','seo-file-names'), 
                    'value'  => '',     
                ),
                'blogdesc'  => array(
                    'title'  => __('Site description','seo-file-names'),
                    'desc'   => __('The site description','seo-file-names'),
                    'value'  => '', 
                ),
                'filename'  => array(
                    'title'  => __('Original filename','seo-file-names'),
                    'desc'   => __('The sanitized orginal filename, usefull to keep track of your local files','seo-file-names'),
                    'value'  => '', 
                ),
                'replace-1' => array(
                    'title'  => esc_html(__('Search & replace rule 1','seo-file-names')),
                    'desc'   => __('Search & replace rule from the search & replace fields','seo-file-names'),
                    'value'  => '', 
                ),

            );
            
            $this->_options['datas'] = array(
                    'id'=> 'id',
                    'title' => 'string',
                    'slug' => 'string',
                    'cat' => 'ids',
                    'tag' => 'ids_string',
                    'author' => 'id',
                    'type' => 'string',
                    'taxonomy' => 'string',
                    'tmp_post' => 'id',
                    'tmp_tag' => 'id',
            );
        }
}