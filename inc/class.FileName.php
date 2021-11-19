<?php

defined( 'ABSPATH' ) || exit;

if(class_exists('asf_FileName')) return;

/**
* Filename rewrite
* @since 0.9.0
*/
class asf_FileName {

    private $_originalFilename;
    private $_sanitize;

    public function __construct() {
        $this->_sanitize = new asf_Sanitize;
    } 

    /**
    * Rewrite file name
    * @since 0.9.0
    */
    public function rewriteFileName($file) {

        $userOptions = $this->getUserOptions();

        if($userOptions && isset($userOptions['is_paused']) && $userOptions['is_paused'] == '1') return $file;

        $fileName = sanitize_file_name($file['name']);
        $this->_originalFilename = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        $name = $this->fileName($userOptions);
        if(!$name) return $file;

        $file['name'] = sanitize_file_name($name.'.'.$ext);

        return $file;
    }

    /**
    * Filename merge Options and Tags
    * @since 0.9.0
    */
    private function fileName($userOptions = false) {
        $options = $this->fillOptions();
        if(!$options) return false;
        $fileName = false;
        
        if( $userOptions && isset($userOptions['default_schema']) && !empty($userOptions['default_schema']) ) {
            $fileName = $this->replaceTags($options, $userOptions['default_schema']);
        } else {
            $fileName = $this->replaceTags($options, $options['options']['default_schema']);
        }

        return $fileName;
    }

    /**
    * Replace Tags
    * @since 0.9.0
    */
    private function replaceTags($options, $schema) {
        $fileName = false;
        if( isset($options['tags']) && !empty($options['tags'])) {
            foreach($options['tags'] as $key => $array) {
                if($array['value']) {
                    $schema = $fileName ? $fileName : $schema;
                    $fileName = str_replace('%'.$key.'%', '-'.$array['value'].'-', $schema);
                } else {
                    $schema = $fileName ? $fileName : $schema;
                    $fileName = str_replace('%'.$key.'%', '', $schema);
                }
            }
            $fileName = preg_replace('/\-{2,}/', '-', $fileName);
            $fileName = preg_replace('/^\-{1,}/', '', $fileName);
            $fileName = preg_replace('/\-{1,}$/', '', $fileName);
        }
        return $fileName;
    }

    /**
    * Get current id
    * @since 0.9.0
    */
    private function getCurrentId() {
        $id = false;

        $userId = asf_getCurrentUserId();
        $usersDatas = asf_getUsersData();

        switch(true) {
            case get_queried_object_id() :
                $id = get_queried_object_id();
                break;
            case isset($_POST['post_id']) && $this->_sanitize->sanitizeId($_POST['post_id']) :
                $postId = $this->_sanitize->sanitizeId($_POST['post_id']);
                if($post = get_post($postId)) {
                    $id = $post->ID;
                    $usersDatas[$userId]['tmp_post'] = false;
                    update_option('asf_tmp_options',array('datas' => $usersDatas));
                } 
                break;
            case isset($_GET['tag_ID']) && $this->_sanitize->sanitizeId($_GET['tag_ID']) :
                $postId = $this->_sanitize->sanitizeId($_POST['post_id']);
                if($post = get_post($postId)) {
                    $id = $post->ID;
                    $usersDatas[$userId]['tmp_post'] = false;
                    update_option('asf_tmp_options',array('datas' => $usersDatas));
                } 
                break;
        }

        return $id;
    }

    /**
    * Fill Options
    * @since 0.9.0
    */
    private function fillOptions() {
        
        $options = $this->getOptions();
        $userDatas = $this->getUserDatas($options);
        
        if( !isset($options['tags']) && !is_array($options['tags']) ) return false;
        
        $options = $this->fillUserOptions($options, $userDatas);
        $options = $this->fillGlobalOptions($options);
        return $options;
    }

    /**
    * Fill User Options
    * @since 0.9.3
    */
    private function fillUserOptions($options, $userDatas) {
        //$postId = $this->getCurrentId();
        if(!$postId && $userDatas['id']) $postId = $userDatas['id'];
        foreach ($options['tags'] as $key => $array) {
            
            $value = $userDatas && is_array($userDatas) && array_key_exists($key, $userDatas) && !empty($userDatas[$key]) && $userDatas[$key] != false ? $userDatas[$key] : false;
            if(!$postId && !$value) continue;
                
                switch($key) {
                    case 'title' :
                        $options['tags'][$key]['value'] = $value ? $value : $this->getTheTitle($postId);
                    break;
                    case 'slug' :
                        $options['tags'][$key]['value'] = $value ? $value : $this->getSlug($postId);
                    break;
                    case 'type' :
                        $options['tags'][$key]['value'] = $value ? $value : $this->getPostType($postId);
                    break;
                    case 'tag' :
                        $options['tags'][$key]['value'] = $value ? $this->getTermSlug($value) : $this->getFirstTag($postId);
                    break;
                    case 'cat' :
                        $options['tags'][$key]['value'] = $value ? $this->getTermSlug($value) : $this->getFirstCat($postId);
                    break;
                    case 'author' :
                        $options['tags'][$key]['value'] = $value ? $this->getAuthorName($value) : $this->getAuthor($postId);
                    break;
                    case 'taxonomy' :
                        $options['tags'][$key]['value'] = $value ? $this->getTaxonomyName($value) : $this->getTaxonomyName($postId);
                    break;
                    case 'datepublished' :
                        $options['tags'][$key]['value'] = $this->getDatePublished($postId);
                    break;
                    case 'datemodified'  :
                        $options['tags'][$key]['value'] = $this->getDateModified($postId);
                    break;
                } 
        }


        return $options;
    }

    /**
    * Fill Global Options
    * @since 0.9.3
    */
    private function fillGlobalOptions($options) {
        
        foreach ($options['tags'] as $key => $array) {
    
            switch($key) {
                case 'blogname' :
                $options['tags'][$key]['value'] = sanitize_title(sanitize_option('blogname',get_bloginfo('name')));
                break;
                case 'blogdesc' :
                    $options['tags'][$key]['value'] = sanitize_title(sanitize_option('blogdescription',get_bloginfo('description')));
                break;
                case 'filename' :
                    $options['tags'][$key]['value'] = sanitize_title($this->_originalFilename);
                break;
            } 
        }

        return $options;
    }

    /**
    * Get default options from asf_options::
    * @since 0.9.3
    */
    private function getOptions() {
        $options = new asf_options;
        return $options->getOptions();
    }

    /**
    * Get user options from db option 'asf_options'
    * @since 0.9.3
    */
    private function getUserOptions() {
        $options = $this->getOptions();
        $userOptions = get_option('asf_options');
        return $this->_sanitize->sanitizeUserOptions($userOptions,$options);
    } 

    /**
    * Get user datas from db option 'asf_tmp_options'
    * @since 0.9.3
    */
    private function getUserDatas($options) {
        
        $userId = $this->_sanitize->sanitizeId(get_current_user_id());
        if(!$userId) return false;

        $userValues = get_option('asf_tmp_options');

        return isset($userValues['datas'][$userId]) ? $this->_sanitize->sanitizeTmpDatas($options['datas'], $userValues['datas'][$userId]) : false;
    }

    /**
    * Get title from id for WP_Post and WP_Term
    * @since 0.9.0
    */
    private function getTheTitle($postId) {
        $title = false;
        
        $post = get_post($postId);
        if (is_a($post, 'WP_Post')) {
            $title = sanitize_title(get_the_title($postId));   
        }

        $term = get_term($postId);
        if (is_a($term, 'WP_Term')) {
            $title = sanitize_title($term->name);
        }

        return $title;
    }

    /**
    * Get slug from id for WP_Post and WP_Term
    * @since 0.9.0
    */
    private function getSlug($postId) {
        $slug = false;
        
        $post = get_post($postId);
        if(is_a($post, 'WP_Post')) {
            $slug = $post->post_name;   
        }

        $term = get_term($postId);
        if(is_a($term, 'WP_Term')) {
            $slug = $term->slug;
        }

        return $slug;
    }

    /**
    * Get post type from id for WP_Post
    * @since 0.9.0
    */
    private function getPostType($postId) {
        $type = false;

        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;

        $obj = get_post_type_object(get_post_type($postId));
        if(is_a($obj,'WP_Post_Type')) {
            $type = sanitize_title($obj->labels->singular_name);
        }

        return $type;
    }

    /**
    * Get first post_tag slug from WP_Post id
    * @since 0.9.0
    */
    private function getFirstTag($postId) {
        $tag = false;

        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;
        
        $tags = get_the_tags($postId);
        if( $tags && is_array($tags) && isset($tags[0]) && is_a($tags[0], 'WP_Term') ) {
            $tag = $tags[0]->slug;
        }

        return $tag;
    }

    /**
    * Get first category slug from WP_Post id
    * @since 0.9.0
    */
    private function getFirstCat($postId) {
        $cat = false;

        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;
        
        $cats = get_the_category($postId);
        if( $cats && is_array($cats) && isset($cats[0]) && is_a($cats[0], 'WP_Term') ) {
            $cat = $cats[0]->slug;
        }

        return $cat;
    } 

    /**
    * Get Term Slug from user input
    * @since 0.9.1
    */
    private function getTermSlug($value) {
        $slug = false;

        if(is_string($value) && !preg_match('/^\d+$/', $value)) return $this->_sanitize->sanitizeString($value);
        
        $termIds = is_array($value) ? $value : array($value);
        $termId = $this->_sanitize->sanitizeId($termIds[0]);
        $term = get_term($termId);
        if (is_a($term, 'WP_Term')) {
            $slug = $term->slug;
        }
        if(!$slug) {
            $slug = $this->_sanitize->sanitizeString($termIds[0]);
        }
        return $slug;
    }

    /**
    * Get author name from WP_Post id
    * @since 0.9.0
    */
    private function getAuthor($postId) {
        $author = false;

        $post = get_post($postId);
        if (is_a($post, 'WP_Post')) {
            $authorId = $post->post_author;
            $author = $this->getAuthorName($authorId);
        }

        return $author;
    }

    /**
    * Get author name from author id user input
    * @since 0.9.0
    */
    private function getAuthorName($authorId) {
        $authorName = false;

        $author = get_the_author_meta('display_name', $authorId);
        if($author) {
            $authorName = sanitize_title(sanitize_user($author,true));
        }
        
        return $authorName;
    }

    /**
    * Get taxonomy name from WP_Term id
    * @since 0.9.0
    */
    private function getTaxonomyName($value) {
        $taxonomyName = false;

        if(is_string($value) && !preg_match('/^\d+$/', $value)) return $this->_sanitize->sanitizeString($value);

        $term = get_term($value);
        if (!is_a($term, 'WP_Term')) return false;
        
        $taxonomy = get_taxonomy($term->taxonomy);
        if (is_a($taxonomy, 'WP_Taxonomy')) {
            $taxonomyName = sanitize_title($taxonomy->labels->singular_name);
        }
        return $taxonomyName;
    }

    /**
    * Get date published from WP_Post id
    * default to current date
    * @since 0.9.0
    */
    private function getDatePublished($postId) {
        $date = false;

        $post = get_post($postId);
        if (is_a($post, 'WP_Post')) {
            $date = get_the_date('Y-m-d',$postId);
        }

        if (!$date) {
            $date = date('Y-m-d');
        }

        return $date;
    }

    /**
    * Get date modified from WP_Post id
    * default to current date
    * @since 0.9.0
    */
    private function getDateModified($postId) {
        $date = false;

        $post = get_post($postId);
        if (is_a($post, 'WP_Post')) {
            $date = get_the_modified_date('Y-m-d',$postId);
        }
        
        if (!$date) {
            $date = date('Y-m-d');
        }

        return $date;
    }


}