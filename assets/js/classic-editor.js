jQuery(document).ready(function($) {

	if($('#post_tag').length == 0) return;

	asf_tagChanges();
	asf_catChanges();
	const datas = {
	    	'title':'',
	 		'slug':'',
	 		'cat': new Array(),
	 		'tag': new Array(),
	 		'author':'',
	 		'type':'',
	};

	/**
	* Title
	*/
	var title = $('#title');
	val = sanitizeText(title);
	if(val) {
		datas.title = val;
	}
	title.on('blur',function() {
		val = sanitizeText($(this));
		if(val) {
			datas.title = val;
			asf_ajax(datas);
		}
	});

	/**
	* Slug
	*/
	var slug = $('#post_name');
	val = sanitizeText(slug);
	if(val) {
		datas.slug = val;
	}

	slug.on('change',function(){
		val = sanitizeText($(this));
		if(val) {
			datas.slug = val;
			asf_ajax(datas);
		}
	});

	/**
	* Cats
	*/
	var catEl = $('#categorychecklist');
	var catEls = $('input',catEl);

	catEls.each(function(){
		if($(this).prop( "checked" )) {
			datas.cat.push($(this).val());
		}
	});

	catEls.each(function(){
		$(this).on('change',function(){
			datas.cat = new Array();
			$('#categorychecklist input').each(function(){
				if($(this).prop( "checked" )) {
					datas.cat.push($(this).val());
				}
			});
			asf_ajax(datas);
		});
	});

	catEl.on('cat-changed',function(){
		datas.cat = new Array();
		$('input',this).each(function() {
			if($(this).prop( "checked" )) {
				datas.cat.push($(this).val());
			}
		});
		asf_ajax(datas);
	});	

	/**
	* Tags
	*/
	var tagEl = $('#post_tag');

	$('.tagchecklist li',tagEl).each(function() {
		var text = asf_firstTextNode($(this)[0]);
		datas.tag.push(text);
	});

	tagEl.on('tag-changed',function(){
		datas.tag = new Array();
		$('.tagchecklist li',this).each(function() {
			var text = asf_firstTextNode($(this)[0]);
			datas.tag.push(text);
		});
		asf_ajax(datas);
	});	

	/**
	* Author
	*/
	var authorEl = $('#post_author_override');
	if(authorEl.val()) {
		datas.author = authorEl.val();
	} 
	authorEl.on('change',function(){
		datas.author = $(this).val();
		asf_ajax(datas);
	});

	/**
	* Post Type
	*/
	var postType = $('#post_type');
	if(postType.val()) {
		datas.type = postType.val();
		asf_ajax(datas);
	} 

	asf_ajax(datas);//First Post

	window.onfocus = function() { 
	    asf_ajax(datas);
	};

	function asf_ajax(datas) {
		$.ajax({
            type : 'POST',
            url : asfAjax.ajaxurl,
            data : {
                action: 'asf_save_meta',
                asf_datas: JSON.stringify(datas),
                asf_nonce: asfAjax.nonce,
            },
            success: function(response) {
               //console.log(response);
            },
            error : function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            },
    	});
	}

	function sanitizeText(input) {
		var val = input.val();
		if(!val) return;
		
		val = asf_StripHtml(val).trim();
		if(!val.length) return;
		return val;
	}
});//END jQuery

function asf_tagChanges() {

	var targetNode = document.getElementById('post_tag');
	var config = { attributes: false, childList: true, subtree: true, };

	var callback = function(mutationsList) {
		let event = new Event("tag-changed", {bubbles: true});
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'childList') {
	            targetNode.dispatchEvent(event);
	        }
	    }
	};

	var observer = new MutationObserver(callback);
	observer.observe(targetNode, config);
	
}

function asf_catChanges() {

	var targetNode = document.getElementById('categorychecklist');
	var config = { attributes: false, childList: true, subtree: true, };

	var callback = function(mutationsList) {
		let event = new Event("cat-changed", {bubbles: true});
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'childList') {
	            targetNode.dispatchEvent(event);
	        }
	    }
	};

	var observer = new MutationObserver(callback);
	observer.observe(targetNode, config);
	
}

function asf_StripHtml(html){
   let doc = new DOMParser().parseFromString(html, 'text/html');
   return doc.body.textContent || "";
}

function asf_firstTextNode(el) {
	var firstText = '';
	if(el.childNodes === undefined) return;
	for (var i = 0; i < el.childNodes.length; i++) {
	    var curNode = el.childNodes[i];
	    if (curNode.nodeType == Node.TEXT_NODE) {
	    	if(curNode.nodeValue.trim() != '') {
		        return curNode.nodeValue;
		    }
    	}
	}
}