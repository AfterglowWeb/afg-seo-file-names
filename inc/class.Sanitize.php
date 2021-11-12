<?php

defined( 'ABSPATH' ) || exit;

if(class_exists('asf_Sanitize')) return;

/**
 * Sanitize methods
 * @since 0.9.3
 */
class asf_Sanitize {

	public function __construct() {

	}

	/**
	* Sanitize ID
	* @since 0.9.3
	*/
	public function sanitizeId($id) {
        $id = preg_replace("/[^0-9]/", "", $id);
        return !empty($id) ? (int) $id : false;
    }

    /**
	* Sanitize Array of IDs
	* @since 0.9.3
	*/
	public function sanitizeIds($ids) {
		
		if(!array($ids)) return false;
        
        foreach($ids as $intKey => $id) {
        	if(!$this->sanitizeId($id)) {
        		unset($ids[$intKey]);
        		continue;
        	}
            $ids[$intKey] = $this->sanitizeId($id);
        }
        
        if(empty($ids)) return false;
        
        return $ids;
    }

    /**
	* Sanitize String
	* @since 0.9.3
	*/
	public function sanitizeString($string) {
		$string = sanitize_text_field($string);
		$string = sanitize_title(remove_accents($string));
		if(empty($string)) return false;
        return $string;
    }

    /**
	* Sanitize Term Id
	* @since 0.9.3
	*/
	public function sanitizeTermId($term) {
	    $termId = sanitize_term_field('term_id',$term->term_id,$term->term_id,$term->taxonomy,'db');
	    if(empty($termId)) return false;
        return $termId;
	}

	/**
	* Sanitize Term Ids
	* @since 0.9.3
	*/
	public function sanitizeTermIds($termIds) {
		if(!array($termIds)) return false;
        
        foreach($termIds as $intKey => $id) {
        	$term = get_term($id);
        	if (is_a($term, 'WP_Term')) {
        		if(!$this->sanitizeTermId($term)) {
        			unset($termIds[$intKey]);
        			continue;
        		}
        		$termIds[$intKey] = $this->sanitizeTermId($term);
        	}
        }

	    if(empty($termIds)) return false;

        return $termIds;
	}

	/**
	* Sanitize db option 'asf_tmp_options'
	* @since 0.9.3
	*/
	public function sanitizeTmpDatas($options,$datas = array()) {
	    
	    if(!is_array($datas) || empty($datas)) return false;

	    foreach ($datas as $key => $value) {
	        if(!array_key_exists($key, $options['datas'])) {
	        	unset($datas[$key]);
	        	continue;
	        }

	        switch ($options['datas'][$key]) {

	            case 'string':
	            	if(!$this->sanitizeString($value)) {
	            		unset($datas[$key]);
	            		break;
	            	}
	                $datas[$key] = $this->sanitizeString($value);
	                break;

	            case 'id':
	            	if(!$this->sanitizeId($value)) {
	            		unset($datas[$key]);
	            		break;
	            	} 
		            $datas[$key] = $this->sanitizeId($value);
	                break;

	            case 'ids':
	                if(!is_array($value)) break;
	                $value = $this->sanitizeTermIds($value);
		                
	                if(!$value) {
		                unset($datas[$key]);
		                break;
		            } 

	                $datas[$key] = $value;
	                break;

	            case 'ids_string' :
	                if(is_array($value)) {
	                	$value = $this->sanitizeTermIds($value);
		                if(!$value) {
		                	$value = $this->sanitizeString($value[0]);
			            }
		                if(!$value) {
			                unset($datas[$key]);
			                break;
			            }

	                    $datas[$key] = $value;
	                    break;
	                }
	                if(is_string($value)) {
	                	if(!$this->sanitizeString($value)) {
		            		unset($datas[$key]);
		            		break;
		            	}
		                $datas[$key] = $this->sanitizeString($value);
	                    break;
	                }
	                break;

	        }
	    }
	    if(empty($datas)) return false;
	    return $datas;
	}

	/**
    * Sanitize db option 'asf_options'
    * @since 0.9.3
    */
    public function sanitize( $input ) {
        if( isset( $input['default_schema'] ) && !empty( trim($input['default_schema']) ) ) {
            $input['default_schema'] = $this->sanitizeSchema($input['default_schema']);
        }
        if( isset( $input['is_paused'] ) ) {
            $input['is_paused'] = '1';
        }
        return $input;
    }

    /**
	* Sanitize Schema Field
	* @since 0.9.3
	*/
    public function sanitizeSchema($schema) {
        $schema = strtolower($this->asf_sanitizeTextFields($schema));
        $schema = str_replace(' ', '-', $schema);
        $schema = preg_replace("/[^a-z0-9\-%]/", "", $schema);
        return $schema;
    }

    /**
	* Sanitize User Options
	* @since 0.9.3
	*/
    public function sanitizeUserOptions($userOptions,$options) {
            if(!is_array($userOptions)) return false;
            foreach($userOptions as $key => $value) {
                if(!array_key_exists($key, $options)) continue;
                switch($key) {
                    case 'default_schema' :
                        $userOptions[$key] = $value ? $this->sanitizeSchema($value) : '';
                        break;
                    case 'is_paused' :
                        $userOptions[$key] = '1';
                        break;
                }
            }
            return $userOptions;
     }

    /**
	* Sanitize Json
	* @since 0.9.3.1
	*/
    public function sanitizeJson($json) {
    	$json = sanitize_text_field($json);
    	if(empty($json)) return false;
       	$json = trim(preg_replace('/[^0-9a-zA-Z\-:",{}\[\]]/', '', $json));
        if(empty($json)) return false;
        return $json;
     }

    /**
    * Variation of '_sanitize_text_fields' native WP function
    * https://developer.wordpress.org/reference/functions/_sanitize_text_fields/
    * Removed the hexadecimal filter to keep "%" separators
    * @since 0.9.1, moved to asf_Sanitized:: on 0.9.3
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

}//END Class