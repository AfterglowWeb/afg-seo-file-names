<?php

defined( 'ABSPATH' ) || exit;

if(class_exists('asf_Sanitize')) return;

/**
 * Sanitize methods
 * @since 0.9.3
 */
class asf_Sanitize {

	private $_options;

	public function __construct() {
		$options = new asf_options;
		$this->_options = $options->getOptions();
	}

	/**
	* Sanitize db user option 'asf_options'
	* @since 0.9.3
	*/
    public function sanitizeUserOptions($userOptions) {
            
            if(!is_array($userOptions)) return false;

            if(!is_array($this->_options)) return false;
            
            foreach($userOptions as $key => $value) {
                
                if(!array_key_exists($key, $this->_options['options'])) {
                	
                	unset($userOptions[$key]);

                	continue;
                } 

                switch($key) {
                    case 'default_schema' :
                        $userOptions[$key] = $value ? $this->sanitizeSchema($value) : '';
                        break;
                    case 'is_paused' :
                        $userOptions[$key] = '1';
                        break;
                   	case 'default_users' :
                   		$userOptions[$key] = $value ? $this->sanitizeIds($value) : '';
                   		break;
                   	case 'default_search_replace' :
                   		$userOptions[$key] = $value && is_array($value) ? $value : '';
                   		break;
                   	case 'default_search_replace_options' :
                   		$userOptions[$key] = $value && is_array($value) ? $value : '';
                   		break;
                }
            }

            return $userOptions;

    }

    /**
	* Sanitize db user option 'asf_tmp_options'
	* @since 0.9.3
	*/
	public function sanitizeTmpDatas($datas) {

	    if(!is_array($datas)) return false;

	    $options = $this->_options['datas'];

	    foreach ($datas as $key => $value) {
	        if(!array_key_exists($key, $options)) {
	        	unset($datas[$key]);
	        	continue;
	        }

	        switch ($options[$key]) {

	            case 'string':
	            	if(!$this->sanitizeString($value,true)) {
	            		unset($datas[$key]);
	            		break;
	            	}
	                $datas[$key] = $this->sanitizeString($value,true);
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
		                	$value = $this->sanitizeString($value[0],true);
			            }
		                if(!$value) {
			                unset($datas[$key]);
			                break;
			            }

	                    $datas[$key] = $value;
	                    break;
	                }
	                if(is_string($value)) {
	                	if(!$this->sanitizeString($value,true)) {
		            		unset($datas[$key]);
		            		break;
		            	}
		                $datas[$key] = $this->sanitizeString($value,true);
	                    break;
	                }
	                break;

	        }
	    }

	    if(empty($datas)) return false;

	    return $datas;
	}

	/**
	* Sanitize ID
	* 
	* @param int || string
	* 
	* @return int || false
	* 
	* @since 0.9.3
	*/
	public function sanitizeId($id) {

        $id = preg_replace("/[^\d]/", "", $id);

        return !empty($id) ? (int) $id : false;
    }

    /**
	* Sanitize array of IDs
	* 
	* @param array(int||string,...)
	* 
	* @return array(int,...) || false
	* 
	* @since 0.9.3
	*/
	public function sanitizeIds($ids) {
		
		if(!array($ids)) return false;
        
        if(empty($ids)) return false;
        
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
	* Sanitize string
	* 
	* @param string, bol
	* 
	* @return string || false
	* 
	* @since 0.9.3
	*/
	public function sanitizeString($string, $strict = true) {
		
		if( empty(mb_strlen($string, 'UTF-8')) && $strict) return false;
		
		if( empty(mb_strlen($string, 'UTF-8')) ) return '';
		
		$string = sanitize_text_field($string);

		$string = trim(preg_replace('/[^\w:",{}\[\] -]/', '-', remove_accents($string) ) );

		if($strict) $string = trim(preg_replace('/[^A-Za-z0-9 -]/', '-', remove_accents($string) ) );

		$string = $this->normalizeDashes($string);
		
		if( empty(mb_strlen($string, 'UTF-8')) && $strict) return false;
		
		if( empty(mb_strlen($string, 'UTF-8')) ) return '';

        return $string;
    }

	/**
	* Sanitize array of strings
	* 
	* @param array(string,...), bol
	* 
	* @return array(string,...) || false
	* 
	* @since 0.9.4
	*/
    public function sanitizeStrings($array, $strict = true) {
    	
    	if(!array($array)) return false;
        
        if(empty($array) && $strict) return false;
        
        foreach ($array as $key => $string) {
        	
        	$array[$key] = $this->sanitizeString($string, $strict);
        }
        return $array;
    }

    /**
	* Sanitize Term Id
	* 
	* @param WP_Term Object
	* 
	* @return int || false
	* 
	* @since 0.9.3
	* 
	*/
	public function sanitizeTermId($term) {
	    
	    $termId = sanitize_term_field('term_id',$term->term_id,$term->term_id,$term->taxonomy,'db');
	    
	    if(empty($termId)) return false;
        
        return $termId;
	}

	/**
	* Sanitize Term Ids
	* 
	* @param array(int,...)
	* 
	* @return array(int,...) || false
	* 
	* @since 0.9.3
	* 
	*/
	public function sanitizeTermIds($termIds) {

		if(!array($termIds)) return false;
        
        foreach($termIds as $key => $id) {
        	
        	$term = get_term($id);
        	
        	if (is_a($term, 'WP_Term')) {
        		
        		if(!$this->sanitizeTermId($term)) {
        			
        			unset($termIds[$key]);
        			
        			continue;
        		}

        		$termIds[$key] = $this->sanitizeTermId($term);
        	}
        }

	    if(empty($termIds)) return false;

        return $termIds;
	}

    /**
	* Sanitize Schema Field
	* 
	* @param string
	* 
	* @since 0.9.3
	* 
	* @return string || false
	* 
	*/
    public function sanitizeSchema($schema) {
        
        $schema = strtolower($this->asf_sanitizeTextFields($schema));
        
        $schema = str_replace(' ', '-', $schema);
        
        $schema = preg_replace("/[^a-z0-9\-%]/", "", $schema);
        
        if(empty($schema)) return false;
        
        return $schema;
    }

    /**
	* Sanitize '1' || '0' Field
	* 
	* @param string
	* 
	* @return string
	* 
	* @since 0.9.3
	* 
	*/
    public function sanitizeBooleanField($value = false) {
        
        if( !empty(mb_strlen($value, 'UTF-8')) && $value == '1') {

        	return '1';

        }

        return '0';
    }

    /**
	* Sanitize '1' || '0' Fields
	* 
	* @param array
	* 
	* @return array || false
	* 
	* @since 0.9.3
	* 
	*/
    public function sanitizeBooleanFields($array) {

    	if(!is_array($array)) return false;

    	foreach ($array as $key => $value) {

    		$array[$key] = $this->sanitizeBooleanField($value);

    	}
        return $array;
    }

    /**
	* Sanitize Json
	* 
	* Sanitize Json coming from Ajax posts
	* Prepare values for filenames by replacing accents and keeping only [0-9] [a-z] [A-Z] chars
	* 
	* @param string
	* 
	* @return string || false
	* 
	* @since 0.9.31
	* 
	*/
    public function sanitizeJson($json) {
    	
    	$json = sanitize_text_field($json);
    	
    	if(empty($json)) return false;
       	
       	$json = trim(preg_replace('/[^0-9a-zA-Z\-:",{}\[\] ]/', '', remove_accents($json)));
        
        if(empty($json)) return false;
        
        return $json;
     }

    /**
    * Variation of '_sanitize_text_fields' native WP function
    * without the hexadecimal filter to keep "%" separators
    * 
    * https://developer.wordpress.org/reference/functions/_sanitize_text_fields/
    * 
    * @since 0.9.1, moved to asf_Sanitized:: on 0.9.3
    * 
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

    /**
	* Sanitize input name attribute
	* 
	* @param array()
	* 
	* @return string || false
	* 
	* @since 0.9.4
	*/
    public function sanitizeInputName($value) {

    	$name = is_array($value) ? $value : array($value);

    	$name = $this->sanitizeStrings($name,false);

    	if(!$name) return false;

		$name = implode('][', $name);

		return 'asf_options['.$name.']';

    }

    /**
    * Replace 2+ dashes by 1 dash
	* Remove starting and trailing dashes 
	* 
	* @param string
	* 
	* @return string || false
	* 
	* @since 0.9.4
	*/
    public function normalizeDashes($string) {
    	
    	$string = preg_replace('/\-{2,}/', '-', $string);

        $string = preg_replace('/^\-{1,}/', '', $string);

        $string = preg_replace('/\-{1,}$/', '', $string);

        if( empty(mb_strlen($string, 'UTF-8')) ) return false;

        return $string;
    }

    /**
	* 
	* @param string
	* 
	* @return true || false
	* 
	* @since 0.9.4
	*/
    public function isEmpty($string) {
	    return empty(mb_strlen($string, 'UTF-8'));
	}

    /**
    * Sanitize Repeater Field
    * 
    * $userValue = array of rows, row = array of user values
    * $key = repeater field key, must match a key in from asf_options::->_options['options']
    * 
    * @param array, string
    * 
    * @return array || false
    * 
    * @since 0.9.4
    * 
    */
    public function sanitizeRepeaterField($datas,$key) {
    	
    	if(!is_array($datas)) return false;

    	if(!isset($this->_options['options'][$key]['rows'][0]['fields'])) return false;

    	if(!is_array($this->_options['options'][$key]['rows'][0]['fields'])) return false;
    	
    	$fieldsModel = $this->_options['options'][$key]['rows'][0]['fields'];

    	foreach($datas as $rowNumber => $row) {

    		if(!is_array($row)) continue;

    		foreach($row as $fieldKey => $value) {

	    			if(!array_key_exists($fieldKey, $fieldsModel)) {
	                	unset($datas[$rowNumber][$fieldKey]);
	                	continue;
	                } 

	               	$value = sanitize_text_field($value);

	                if( isset($fieldsModel[$fieldKey]['type']) ) {
	                	
	                	$fieldType = sanitize_key($fieldsModel[$fieldKey]['type']);
		            	
		            	$value = $this->sanitizeFieldByType($value, $fieldType, false);

	                }

	                if( isset($fieldsModel[$fieldKey]['args']['pattern']) ) {
	                	
	                	$regex = sanitize_text_field($fieldsModel[$fieldKey]['args']['pattern']);

		                if($regex != '') $value = preg_replace('/^(?:(?!'.$regex.'))$/', '', $value);

		            }
		            
	                if($value !== false) $datas[$rowNumber][$fieldKey] = $value;

	    	}

    	}

    	return $datas;
    }

    /**
    * Sanitize Field By Type
    * 
    * $value = field value
    * 
    * $type = field type registerd in asf_options::getOptions[options]
    * 
    * $strict (opt.) = 
    * 	true: return false on empty field, 
    * 	false: return empty string on empty field
    * 
    * @param array, string (opt.), boolean (opt.)
    * 
    * @return string || false
    * 
    * @since 0.9.4
    * 
    */
    public function sanitizeFieldByType($value, $type, $strict = false) {
   
    	if(!$type) return false;
     	
     	if( sanitize_key($type) == '') return false;
     	
     	$type = sanitize_key($type);

     	switch($type) {
     		case 'checkbox-boolean' :
     			
     			$value = $this->sanitizeBooleanField($value);
     			
     			break;

     		case 'checkbox' :
     		case 'text' :

     			$value = remove_accents(sanitize_text_field($value));

     			if($strict && empty(mb_strlen($value, 'UTF-8'))) return false;

     			break;

     		case 'text-filtered' :

     			$value = $this->sanitizeString($value, $strict);

     			if($strict && !$value) return false;

     			break;

     		default :

     			if($strict) return false;

     			$value = sanitize_text_field($value);

     			break;
     	}

     	return $value;

     }

}//END Class