<?php

defined( 'ABSPATH' ) || exit;

if(class_exists('asf_FileName')) return;

/**
* Filename rewrite
* @since 0.9.0
*/
class asf_FileName {
    
    private $_sanitize;

    private $_userOptions;

    private $_userDatas;

    private $_tags;

    private $_userTags;

    private $_originalFilename;

    private $_filename;

    public function __construct() {

        $this->_sanitize = new asf_Sanitize;
        $this->_userOptions = $this->getUserOptions();
        $this->_userDatas = $this->getUserDatas();
        $this->_tags = $this->getTagsOptions();
        $this->_userTags = array( 'tags' => array() );
        $this->_originalFilename = false;
        $this->_filename = false;

    }

    /**
    * Rewrite file name
    * 
    * @since 0.9.0
    * 
    */
    public function rewriteFileName($file) {

        if($this->_userOptions && isset($this->_userOptions['is_paused']) && $this->_userOptions['is_paused'] == '1') return $file;

        $fileName = sanitize_file_name($file['name']);

        $this->_originalFilename = pathinfo($fileName, PATHINFO_FILENAME);

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if($this->filterRawFilename()) $this->searchReplace();

        $this->replaceTags();

        $this->searchReplace();

        if(!$this->_filename) return $file;

        $file['name'] = sanitize_file_name($this->_filename.'.'.$ext);

        return $file;
    }

    /**
    * Replace Tags
    * 
    * @since 0.9.0
    * 
    */
    private function replaceTags() {

        $schema = false;

        if(!empty($this->_userOptions['default_schema'])) $schema = $this->_userOptions['default_schema'];

        if(!$schema) return false;

        $fileName = $schema;

        $userTags = $this->_userTags;

        if($this->_userDatas && $this->_tags && $userTags) {

            $userTags = $this->fillUserTags($userTags);
            
        }

        $userTags = $this->fillGlobalTags($userTags);

        $userTags = $this->fillSearchReplaceTags($userTags);

        foreach($userTags['tags'] as $key => $value) {

            if(!empty($value)) {

                $fileName = str_replace('%'.$key.'%', '-'.$value.'-', $fileName);

            } else {

                $fileName = str_replace('%'.$key.'%', '', $fileName);

            }

        }

        $fileName = $this->_sanitize->normalizeDashes($fileName);

        if( $this->_sanitize->isEmpty($fileName) ) return false;

        if($fileName === false) return false;

        $this->_filename = str_split($fileName,255)[0];

    }

    /**
    * Fill User Tags
    * 
    * @since 0.9.3
    * 
    */
    private function fillUserTags($userTags) {

        $tags = $this->_tags;
        $userDatas = $this->_userDatas;
        
        $postId = $this->getCurrentId();
        
        if(!$postId && $userDatas['id']) $postId = $userDatas['id'];
        
        foreach ($tags as $key => $array) {
            
            $value = false;

            if(is_array($userDatas) && array_key_exists($key, $userDatas)) {
                
                if( !$this->_sanitize->isEmpty($userDatas[$key]) ) $value = $userDatas[$key];
            
            }

            if(!$postId && !$value) continue;
                
                switch($key) {
                    case 'title' :
                        $userTags['tags'][$key] = $value ? $value : $this->getTheTitle($postId);
                    break;
                    case 'slug' :
                        $userTags['tags'][$key] = $value ? $value : $this->getSlug($postId);
                    break;
                    case 'type' :
                        $userTags['tags'][$key] = $value ? $value : $this->getPostType($postId);
                    break;
                    case 'tag' :
                        $userTags['tags'][$key] = $value ? $this->getTermSlug($value) : $this->getFirstTag($postId);
                    break;
                    case 'cat' :
                        $userTags['tags'][$key] = $value ? $this->getTermSlug($value) : $this->getFirstCat($postId);
                    break;
                    case 'author' :
                        $userTags['tags'][$key] = $value ? $this->getAuthorName($value) : $this->getAuthor($postId);
                    break;
                    case 'taxonomy' :
                        $userTags['tags'][$key] = $value ? $this->getTaxonomyName($value) : $this->getTaxonomyName($postId);
                    break;
                    case 'datepublished' :
                        $userTags['tags'][$key] = $this->getDatePublished($postId);
                    break;
                    case 'datemodified'  :
                        $userTags['tags'][$key] = $this->getDateModified($postId);
                    break;
                } 
        }


        return $userTags;
    }

    /**
    * Fill Global Tags
    * 
    * @since 0.9.3
    * 
    */
    private function fillGlobalTags($userTags) {
        
        foreach ($this->_tags as $key => $array) {
    
            switch($key) {
                case 'blogname' :
                    $userTags['tags'][$key] = sanitize_title(sanitize_option('blogname',get_bloginfo('name')));
                break;
                case 'blogdesc' :
                    $userTags['tags'][$key] = sanitize_title(sanitize_option('blogdescription',get_bloginfo('description')));
                break;
                case 'filename' :
                    $userTags['tags'][$key] = sanitize_title($this->_originalFilename);
                break;
            } 
        }

        return $userTags;
    }

    /**
    * Fill search and replace tags
    * 
    * @since 0.9.4
    * 
    */
    private function fillSearchReplaceTags($userTags) {

        if(!is_array($this->_tags['replace-1'])) return false;

        foreach ($this->_userTags as $i => $replace) {

            $key = 'replace-'.$i; 

            $userTags['tags'][$key] = $replace;
            
        }

        return $userTags;
    }

    /**
    * Search and replace
    * 
    * @since 0.9.4
    */
    private function searchReplace() {

        if($this->_filename === false && $this->_originalFilename === false) return false;
        
        $fileName = $this->_filename ? $this->_filename : $this->_originalFilename;

        $rules = false;

        if( $this->hasUserOption('default_search_replace') ) $rules = $this->_userOptions['default_search_replace'];
        
        if(!$rules) return false;

        if(!is_array($rules)) return false;

        $n = 0;

        $tags = array();

        foreach($rules as $key => $rule) {

            if( isset($rule['is_paused']) && $rule['is_paused'] !== '0' ) {
                $tags[$key] = null;
                continue;
            }
            if( $this->_sanitize->isEmpty($rule['search']) ) {
                $tags[$key] = null;
                continue;
            }

            $fileName = str_replace($rule['search'], $rule['replace'], $fileName);
            
            if(!$this->_sanitize->isEmpty($rule['replace'])) $tags[$key] = $rule['replace'];
        }
        
        if(!$this->_sanitize->isEmpty($fileName)) $this->_filename = $fileName;
        
        if(!empty($tags)) $this->_userTags = $tags;

        return $n;
    }

    /**
    * Apply search and replace on raw filename?
    * 
    * @since 0.9.4
    */
    private function filterRawFilename() {

        $key = 'default_search_replace_options';
        
        if($this->hasUserOption($key)) {
            
            if(!isset($this->_userOptions[$key]['fields']['filter_raw_filenames'])) return false;

            if($this->_userOptions[$key]['fields']['filter_raw_filenames'] === '0') return false;

            return true;
        }

        return false;
    }

    /**
    * Does User option exist by given key?
    * 
    * @since 0.9.4
    */
    private function hasUserOption($key) {

        if( !isset($this->_userOptions[$key]) ) return false;
        
        if( $this->_sanitize->isEmpty($this->_userOptions[$key]) ) return false;

        return true;

    }

    /**
    * Set user options from db option 'asf_options'
    * 
    * @since 0.9.3
    * 
    */
    private function getUserOptions() {
        
        $userOptions = get_option('asf_options');
        
        return $this->_sanitize->sanitizeUserOptions($userOptions);
    } 

    /**
    * Get user datas from db option 'asf_tmp_options'
    * 
    * @since 0.9.3
    * 
    */
    private function getUserDatas() {
        
        $userId = $this->_sanitize->sanitizeId(get_current_user_id());
        
        if(!$userId) return false;

        $userValues = get_option('asf_tmp_options');

        if(!isset($userValues['datas'][$userId])) return false;

        $userDatas = $this->_sanitize->sanitizeTmpDatas($userValues['datas'][$userId]);

        if(!$userDatas) return false;

        return $userValues['datas'][$userId];
    }

    /**
    * Get tags setup from asf_options::_options['tags'];
    * 
    * @since 0.9.3
    * 
    */
    private function getTagsOptions() {

        $options = new asf_options;
        
        $options = $options->getOptions();
        
        return $options['tags'];
    }

    /**
    * Get current id
    * 
    * @since 0.9.0
    * 
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

                $postId = $this->_sanitize->sanitizeId($_GET['tag_ID']);

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

        if(is_string($value) && !preg_match('/^\d+$/', $value)) return $this->_sanitize->sanitizeString($value, true);
        
        $termIds = is_array($value) ? $value : array($value);
        $termId = $this->_sanitize->sanitizeId($termIds[0]);
        $term = get_term($termId);
        if (is_a($term, 'WP_Term')) {
            $slug = $term->slug;
        }
        if(!$slug) {
            $slug = $this->_sanitize->sanitizeString($termIds[0], true);
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

        if(is_string($value) && !preg_match('/^\d+$/', $value)) return $this->_sanitize->sanitizeString($value, true);

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