/**
* @since 0.9.3
*/
  let asf_sanitize = {};

  asf_sanitize.sanitizeString = (s) => {
      if (s === '') return false;
      s = s.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      s = s.replace(/[^a-zA-Z0-9-]/g, '-');
      var onlySeparators = new RegExp("^[-]+$").test(s);
      if(s.trim().length && !onlySeparators) return s;
      return false;
  }
  
  asf_sanitize.stripHtml = (html) => {
     let doc = new DOMParser().parseFromString(html, 'text/html');
     return asf_sanitize.sanitizeString(doc.body.textContent) || "";
  }

  asf_sanitize.sanitizeText = (val) => {
    if(!val || val == '' || val === undefined) return false;
    
    val = asf_sanitize.stripHtml(val);
    if(!val.length) return false;
    return val;
  }

  asf_sanitize.isInt = (i) => {
    var x;
    if (isNaN(i)) {
      return false;
    }
    x = parseFloat(i);
    return (x | 0) === x;
  }

  asf_sanitize.sanitizeInt = (i) => {
      if (!i || i === '') return false;
      if(!asf_sanitize.isInt(i)) return false;
      i = i.toString().replace(/[^0-9]/g, '');
      if(i.trim().length) return parseInt(i);
      return false;
  }

  asf_sanitize.sanitizeIds = (array) => {
    if( array && array !== undefined && array.length && Array.isArray(array) ) {
        array.forEach(function(value,i){
          if(value = asf_sanitize.sanitizeInt(value)) {
            array[i] = value;
          } else {
            delete array[i];
          }
        });
        if(array.length >=1 && Array.isArray(array)) return array;
        return false;  
    }
  }