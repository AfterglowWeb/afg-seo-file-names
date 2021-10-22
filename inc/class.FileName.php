<?php

defined( 'ABSPATH' ) || exit;

if(class_exists('asf_FileName')) return;

class asf_FileName {

    private $_originalFilename;
    private $_postId;
    private $_userVals;
    private $_userOptions;

    public function __construct() {
        $this->_postId = $this->getCurrentId();
        $this->_userVals = $this->getUserValues();
        $this->_userOptions = $this->getUserOptions();
    } 

    public function rewriteFileName($file) {

        $userOptions = $this->_userOptions;
        if(isset($userOptions['is_paused'])) return $file;

        $this->_originalFilename = pathinfo($file['name'], PATHINFO_FILENAME);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        $name = $this->fileName();
        if(!$name) return $file;

        $file['name'] = $name.'.'.$ext;

        return $file;
    }

    private function fileName() {
        $userOptions = $this->_userOptions;
        $options = $this->fillOptions();
        $fileName = false;
        
        if( isset($userOptions['default_schema']) && !empty($userOptions['default_schema']) && $options ) {
            $fileName = $this->replaceTags($options, $userOptions['default_schema']);
        } elseif( $options ) {
            $fileName = $this->replaceTags($options, $options['options']['default_schema']);
        }

        return $fileName;
    }

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

    private function getCurrentId() {
        $id = false;
        if(isset($_POST['post_id'])) {
            if($post = get_post($_POST['post_id'])) {
                $id = $post->ID;
                update_option('asf_tmp_term',false);
            }     
        }
        if(isset($_GET['tag_ID'])) {
            if($term = get_term($_GET['tag_ID'])) {
                $id = $term->term_id;
                update_option('asf_tmp_term',false);
            }
        }

        if(get_option('asf_tmp_term') != false) {
            $id = get_option('asf_tmp_term');
            update_option('asf_tmp_term',false);
        }


        if(get_option('asf_tmp_post') !== false) {
            $id = get_option('asf_tmp_post');
            update_option('asf_tmp_post',false);
        }

        return $id;
    }

    private function getUserValues() {
        $userValues = get_option('asf_tmp_options');
        return $userValues;
    }

    private function getUserOptions() {
        return get_option('asf_options');
    }

    private function fillOptions() {
        $postId = $this->_postId;
        $userValues = $this->_userVals;
        $userDatas = $this->getUserDatas($userValues);
        $options = new asf_options;
        $options = $options->getOptions();
        
        if( !isset($options['tags']) && !is_array($options['tags']) ) return false;
        
        foreach ($options['tags'] as $key => $array) {
            
            $value = $userDatas && property_exists($userDatas,$key) && !empty($userDatas->$key) ? $userDatas->$key : false;
            
            switch($key) {
                case 'title' :
                    $options['tags'][$key]['value'] = $value ? $value : $this->getTheTitle($postId);
                break;
                case 'slug' :
                    $options['tags'][$key]['value'] = $value ? $value : $this->getSlug($postId);
                break;
                case 'type' :
                    $options['tags'][$key]['value'] = $this->getPostType($postId);
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
                    $options['tags'][$key]['value'] = $this->getTaxonomyName($postId);
                break;
                case 'datepublished' :
                    $options['tags'][$key]['value'] = $this->getDatePublished($postId);
                break;
                case 'datemodified'  :
                    $options['tags'][$key]['value'] = $this->getDateModified($postId);
                break;
                case 'blogname' :
                    $options['tags'][$key]['value'] = sanitize_title(get_bloginfo('name'));
                break;
                case 'blogdesc' :
                    $options['tags'][$key]['value'] = sanitize_title(get_bloginfo('description'));
                break;
                case 'filename' :
                    $options['tags'][$key]['value'] = sanitize_title($this->_originalFilename);
                break;
            } 
        }
        return $options;
    } 

    private function getUserDatas($userValues) {
        return isset($userValues['datas']) && !empty($userValues['datas']) && is_object($userValues['datas']) ? $userValues['datas'] : false;
    }

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

    private function getPostType($postId) {
        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;

        $obj = get_post_type_object(get_post_type($postId));
        if($obj) {
            return sanitize_title($obj->labels->singular_name);
        }
    }

    private function getFirstTag($postId) {
        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;
        
        $tags = get_the_tags($postId);
        if(!$tags) return false;
        return $tags[0]->slug;
    }

    private function getFirstCat($postId) {
        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;
        
        $cats = get_the_category($postId);
        if(!$cats) return false;
        return $cats[0]->slug;
    } 

    private function getTermSlug($termId) {
        $termIds = is_array($termId) ? $termId : array($termId);
        $term = get_term($termIds[0]);
        if (!is_a($term, 'WP_Term')) return false;
        return $term->slug;
    }

    private function getAuthor($postId) {
        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;

        $authorId = $post->post_author;
        return $this->getAuthorName($authorId);
    }

    private function getAuthorName($authorId) {
        $author = get_the_author_meta('display_name', $authorId);
        if(!$author) return false;
        return sanitize_title($author);
    }

    private function getTaxonomyName($postId) {
        $term = get_term($postId);
        if (!is_a($term, 'WP_Term')) return false;
        $taxonomy = get_taxonomy($term->taxonomy);
        if (!is_a($taxonomy, 'WP_Taxonomy')) return false;
        return sanitize_title($taxonomy->labels->singular_name);
    }

    private function getDatePublished($postId) {
        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;
        
        $date = get_the_date('Y-m-d',$postId);
        if (!$date) {
            $date = date('Y-m-d');
        }
        return $date;
    }

    private function getDateModified($postId) {
        $post = get_post($postId);
        if (!is_a($post, 'WP_Post')) return false;
        
        $date = get_the_modified_date('Y-m-d',$postId);
        if (!$date) {
            $date = date('Y-m-d');
        }
        return $date;
    }

}