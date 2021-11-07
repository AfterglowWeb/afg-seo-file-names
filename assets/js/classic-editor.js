jQuery(document).ready(function($) {

	if($('#post_tag').length == 0) return;

	asf_tagChanges();
	const datas = {
	    	'title':'',
	 		'slug':'',
	 		'cats': new Array(),
	 		'tags': new Array(),
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
		}
	});

	/**
	* Cats
	*/
	var catEls = $('#categorychecklist input');

	catEls.each(function(){
		if($(this).prop( "checked" )) {
			datas.cats.push($(this).val());
		}
	});

	catEls.each(function(){
		$(this).on('change',function(){
			datas.cats = new Array();
			catEls.each(function(){
				if($(this).prop( "checked" )) {
					datas.cats.push($(this).val());
				}
			});
		});
	});

	/**
	* Tags
	*/
	var tagEl = $('#post_tag');

	$('.tagchecklist li',tagEl).each(function() {
		var text = asf_firstTextNode($(this)[0]);
		datas.tags.push(text);
	});

	tagEl.on('tag-changed',function(){
		datas.tags = new Array();
		$('.tagchecklist li',this).each(function() {
			var text = asf_firstTextNode($(this)[0]);
			datas.tags.push(text);
		});
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
	});

	/**
	* Post Type
	*/
	var postType = $('#post_type');
	if(postType.val()) {
		datas.type = postType.val();
	} 


	window.onfocus = function() { 
	    asf_ajax(datas);
	};

	function asf_ajax($datas) {
		$.ajax({
            type : 'POST',
            url : asfAjax.ajaxurl,
            data : {
                action: 'asf_save_meta',
                asf_datas: JSON.stringify(datas),
                asf_nonce: asfAjax.nonce,
            },
            success: function(response) {
               
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
		let event = new Event("tag-changed", {bubbles: true}); // (2)
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