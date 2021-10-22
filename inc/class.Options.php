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
                'is_paused' => '1',
            );
            $this->_options['tags'] = array(
                'title' => array(
                    'title'  => __('Title','asf'),
                    'desc'   => __('Current post, page or term title','asf'),     
                    'value'  => '',     
                ),
                'slug' => array(
                    'title'  => __('Slug','asf'),
                    'desc'   => __('Current post, page or term slug','asf'),  
                    'value'  => '',    
                ),
                'type' => array(
                    'title'  => __('Type','asf'),
                    'desc'   => __('Current post or page type, empty on terms','asf'),
                    'value'  => '',      
                ),
                'tag' => array(
                    'title'  => __('Tag','asf'),
                    'desc'   =>  __('Current post or page tag, empty on terms','asf'),
                    'value'  => '',      
                ),
                'cat' => array(
                    'title'  => __('Category','asf'),
                    'desc'   => __('Current post or page category, empty on terms','asf'),
                    'value'  => '',      
                ),
                'author' => array(
                    'title'  => __('Author','asf'),
                    'desc'   => __('Current post or page author, empty on terms','asf'),
                    'value'  => '',      
                ),
                'taxonomy'  => array(
                    'title'  => __('Taxonomy','asf'),
                    'desc'   => __('Current term taxonomy name','asf'), 
                    'value'  => '',     
                ),
                'datepublished' => array(
                    'title'  => __('Date published','asf'),
                    'desc'   => __('Current post or page first published date, empty on terms','asf'),
                    'value'  => '',      
                ),
                'datemodified'  => array(
                    'title'  => __('Date modified','asf'),
                    'desc'   => __('Last date, current post or page as been modified, empty on terms','asf'),
                    'value'  => '',      
                ),
                'blogname' => array(
                    'title'  => __('Site name','asf'),
                    'desc'   => __('The site name','asf'), 
                    'value'  => '',     
                ),
                'blogdesc'  => array(
                    'title'  => __('Site description','asf'),
                    'desc'   => __('The site description','asf'),
                    'value'  => '', 
                ),
                'filename'  => array(
                    'title'  => __('Original filename','asf'),
                    'desc'   => __('The sanitized orginal filename, usefull to keep track of your local files','asf'),
                    'value'  => '', 
                ),
            );
            $this->_options['datas'] = array();
        }
}