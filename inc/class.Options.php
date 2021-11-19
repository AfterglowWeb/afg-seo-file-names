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
                'is_paused' => 1,
                'default_users' => '',
            );
            $this->_options['tags'] = array(
                'title' => array(
                    'title'  => __('Title','seo-file-names'),
                    'desc'   => __('Current post, page or term title','seo-file-names'),     
                    'value'  => '',     
                ),
                'slug' => array(
                    'title'  => __('Slug','seo-file-names'),
                    'desc'   => __('Current post, page or term slug','seo-file-names'),  
                    'value'  => '',    
                ),
                'type' => array(
                    'title'  => __('Type','seo-file-names'),
                    'desc'   => __('Current post or page type. On terms, the type of post to which the term is linked','seo-file-names'),
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