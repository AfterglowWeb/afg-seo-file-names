/**
* @since 0.9.2
*/
jQuery(document).ready(function($) {

	if($('form#post').length == 0 && $('form#edittag').length == 0 && $('form#addtag').length == 0 ) return;
	
	var postType = false; 

	if($('form#post').length) postType = 'post';
	if($('form#edittag').length) postType = 'tag';
	if($('form#addtag').length) postType = 'addTag';

	asf_childNodeChanges($('.tagchecklist').get(0),'tag-changed');
	asf_childNodeChanges($('#categorychecklist').get(0),'cat-changed');
	const datas = {
			'id': false,
	    	'title':'',
	 		'slug':'',
	 		'cat': new Array(),
	 		'tag': new Array(),
	 		'type':'',
	 		'author':'',
	 		'taxonomy':'',
	};

	getId();
	getTitle();
	getSlug();
	getTaxonomy();
	getCategories();
	getTags();
	getAuthor();
	getPostType();

	asf_ajax(datas);//First Post

	window.onfocus = function() { 
	    asf_ajax(datas);
	};

	/**
	* ID
	*/
	function getId() {
		var currentId = false;
		switch(postType) {
			case 'post' :
				currentId = $('input[name="post_ID"]');
				break;
			case 'tag' :
				currentId = $('input[name="tag_ID"]');
				break;
		}
		if(!currentId.length) return false;
		
		if( val = asf_sanitize.sanitizeInt(currentId.val()) ) {
			datas.id = val;
		}
	}

	/**
	* Title
	*/
	function getTitle() {
		var title = false;
		switch(postType) {
			case 'post' :
				title = $('#title');
				break;
			case 'tag' :
				title = $('#name');
				break;
			case 'addTag' :
				title = $('#tag-name');
				break;
		}
		if(!title.length) return false;

		if(val = asf_sanitize.sanitizeText(title.val())) {
			datas.title = val;
		}

		title.on('blur',function() {
			if( val = asf_sanitize.sanitizeText($(this).val()) ) {
				datas.title = val;
				asf_ajax(datas);
			}
		});
	}

	/**
	* Slug
	*/
	function getSlug() {
		var slug = false;
		switch(postType) {
			case 'post' :
				slug = $('#post_name');
				break;
			case 'tag' :
				slug = $('#slug');
				break;
			case 'addTag' :
				slug = $('#tag-slug');
				break;
		}
		
		if(slug.length) {
			if( val = asf_sanitize.sanitizeText(slug.val()) ) {
				datas.slug = val;
			}
		}

		slug.on('change',function(){
			if( val = asf_sanitize.sanitizeText($(this).val()) ) {
				datas.slug = val;
				asf_ajax(datas);
			}
		});
	}

	/**
	* Taxonomy
	*/
	function getTaxonomy() {
		var tagId = false;
		var slug = false;
		switch(postType) {
			case 'tag' :
				tagId = datas.id;
				break;
			case 'addTag' :
				slug = $('input[name="taxonomy"]');
				break;
		}

		if(tagId = asf_sanitize.sanitizeInt(tagId)) {
			datas.taxonomy = tagId;
		}

		if(slug.length) {
			if(slug = asf_sanitize.sanitizeText(slug.val())) {
				datas.taxonomy = slug;
			}
		}
	}

	/**
	* Cats
	*/
	function getCategories() {
		var catEl = $('#categorychecklist');
		var catEls = $('input',catEl);

		if(!catEl.length) return false;

		if(catEls.length) {
			catEls.each(function(){
				if($(this).prop( "checked" )) {
					if(catId = asf_sanitize.sanitizeInt($(this).val())) {
						datas.cat.push(catId);
					} 
				}
			});

			catEls.each(function(){
				$(this).on('change',function(){
					datas.cat = new Array();
					$('#categorychecklist input').each(function(){
						if($(this).prop( "checked" )) {
							if(catId = asf_sanitize.sanitizeInt($(this).val())) {
								datas.cat.push(catId);
							}
						}
					});
					asf_ajax(datas);
				});
			});
		}
		
		catEl.on('cat-changed',function(){
			datas.cat = new Array();
			$('input',this).each(function() {
				if($(this).prop( "checked" )) {
					if(catId = asf_sanitize.sanitizeInt($(this).val())) {
						datas.cat.push(catId);
					}
				}
			});
			asf_ajax(datas);
		});	
		
	}


	/**
	* Tags
	*/
	function getTags() {
		var tagEl = $('#post_tag');
		if(!tagEl.length) return false;
		
		$('.tagchecklist li',tagEl).each(function() {
			var text = asf_firstTextNode($(this)[0]);
			if(text = asf_sanitize.sanitizeText(text)) {
				datas.tag.push(text);
			}
		});

		tagEl.on('tag-changed',function(){
			datas.tag = new Array();
			$('.tagchecklist li',this).each(function() {
				var text = asf_firstTextNode($(this)[0]);
				if(text = asf_sanitize.sanitizeText(text)) {
					datas.tag.push(text);
				}
			});
			asf_ajax(datas);
		});	
	}

	/**
	* Author
	*/
	function getAuthor() {
		var authorEl = $('#post_author_override');
		if(!authorEl.length) return false;

		if(authorEl.val()) {
			if(val = asf_sanitize.sanitizeInt(authorEl.val())) {
				datas.author = val;
			}
		} 
		authorEl.on('change',function(){
			if(val = asf_sanitize.sanitizeInt($(this).val())) {
				datas.author = val;
			}
			asf_ajax(datas);
		});
	}

	/**
	* Post Type
	*/
	function getPostType() {
		switch(postType) {
			case 'post' :
				el = $('#post_type');
				break;
			case 'tag' :
				el = $('#post_type');
				break;
			case 'addTag' :
				el = $('input[name="post_type"]');
				break;
		}
		if(!el.length) return;
		if(el.val()) {
			if(text = asf_sanitize.sanitizeText(el.val())) {
				datas.type = text;
			}
			asf_ajax(datas);
		} 
	}

});//END jQuery

function asf_childNodeChanges(el,eventName) {
	if(el === undefined || el === null) return;
	var config = { attributes: false, childList: true, subtree: true, };

	var callback = function(mutationsList) {
		let event = new Event(eventName, {bubbles: true});
	    for(var mutation of mutationsList) {
	        if (mutation.type == 'childList') {
	            el.dispatchEvent(event);
	        }
	    }
	};

	var observer = new MutationObserver(callback);
	observer.observe(el, config);
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