/**
* @since 0.9.0
*/
const getPostId = () => wp.data.select('core/editor').getCurrentPostId();
const getPostTitle = () => wp.data.select('core/editor').getEditedPostAttribute('title');
const getPostSlug = () => wp.data.select('core/editor').getEditedPostSlug();
const getPostCat = () => wp.data.select('core/editor').getEditedPostAttribute('categories');
const getPostTag = () => wp.data.select('core/editor').getEditedPostAttribute('tags');
const getPostType = () => wp.data.select('core/editor').getEditedPostAttribute('type');

let postId = getPostId();
let title = getPostTitle();
let slug = getPostSlug();
let cat = getPostCat();
let tag = getPostTag();
let type = getPostType();
let authorId = false;

const datas = {
        'id': postId,
    	'title':title,
 		'slug':slug,
 		'cat':cat,
 		'tag':tag,
        'type':type,
        'author':authorId,
    };

wp.data.subscribe(() => {
    setDatas();
});

asf_ajax(datas);

window.onfocus = function() { 
    asf_ajax(datas);
};

function setDatas() {

    const newPostId = getPostId();    
    if( postId !== newPostId && asf_sanitize.sanitizeInt(newPostId) ) {
        datas.id = asf_sanitize.sanitizeInt(newPostId);
        asf_ajax(datas);
    }
    postId = newPostId;

    const newTitle = getPostTitle();    
    if( title !== newTitle  && asf_sanitize.sanitizeText(newTitle) ) {
        datas.title = asf_sanitize.sanitizeText(newTitle);
        asf_ajax(datas);
    }
    title = newTitle;

    const newSlug = getPostSlug(); 
    if( slug !== newSlug && asf_sanitize.sanitizeText(newSlug)  ) {
        datas.slug = asf_sanitize.sanitizeText(newSlug);
        asf_ajax(datas);
    }
    slug = newSlug;

    const newCat = getPostCat();    
    if( cat !== newCat && Array.isArray(newCat) ) {
        datas.cat = newCat;
        asf_ajax(datas);
    }
    cat = newCat;

    const newTag = getPostTag();    
    if( tag !== newTag && Array.isArray(newTag) ) {
        datas.tag = newTag;
        asf_ajax(datas);
    }
    tag = newTag;

    const newType = getPostType();    
    if( type !== newType && asf_sanitize.sanitizeText(newType) ) {
        datas.type = asf_sanitize.sanitizeText(newType);
        asf_ajax(datas);
    }
    type = newType;

    type = asf_sanitize.sanitizeText(type);
    postId = asf_sanitize.sanitizeInt(postId);
    if(type && postId) {
        wp.apiFetch( { path: '/wp/v2/'+type+'/'+ postId } ).then( post => { authorId = post.author; });
        if( asf_sanitize.sanitizeInt(authorId) ) {
            datas.author = asf_sanitize.sanitizeInt(authorId);
            asf_ajax(datas);
        }
    }
}


function asf_ajax(datas) {
    
	asf_ajaxFromJq({
            type : 'POST',
            url : asfAjax.ajaxurl,
            data : {
                action: 'asf_save_meta',
                asf_datas: JSON.stringify(datas),
                asf_nonce: asfAjax.nonce,
            },
            error : function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            },
    });
}


function asf_ajaxFromJq(option) { // $.ajax(...) without jquery.
    if (typeof(option.url) == "undefined") {
        try {
            option.url = location.href;
        } catch(e) {
            var ajaxLocation;
            ajaxLocation = document.createElement("a");
            ajaxLocation.href = "";
            option.url = ajaxLocation.href;
        }
    }
    if (typeof(option.type) == "undefined") {
        option.type = "GET";
    }
    if (typeof(option.data) == "undefined") {
        option.data = null;
    } else {
        var data = "";
        for (var x in option.data) {
            if (data != "") {
                data += "&";
            }
            data += encodeURIComponent(x)+"="+encodeURIComponent(option.data[x]);
        };
        option.data = data;
    }
    if (typeof(option.statusCode) == "undefined") { // 4
        option.statusCode = {};
    }
    if (typeof(option.beforeSend) == "undefined") { // 1
        option.beforeSend = function () {};
    }
    if (typeof(option.success) == "undefined") { // 4 et sans erreur
        option.success = function () {};
    }
    if (typeof(option.error) == "undefined") { // 4 et avec erreur
        option.error = function () {};
    }
    if (typeof(option.complete) == "undefined") { // 4
        option.complete = function () {};
    }
    typeof(option.statusCode["404"]);

    var xhr = null;

    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) { try { xhr = new ActiveXObject("Msxml2.XMLHTTP"); } catch(e) { xhr = new ActiveXObject("Microsoft.XMLHTTP"); } }
        else { xhr = new XMLHttpRequest(); }
    } else { alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest..."); return null; }

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 1) {
            option.beforeSend();
        }
        if (xhr.readyState == 4) {
            option.complete(xhr, xhr.status);
            if (xhr.status == 200 || xhr.status == 0) {
                option.success(xhr.responseText);
            } else {
                option.error(xhr.status);
                if (typeof(option.statusCode[xhr.status]) != "undefined") {
                    option.statusCode[xhr.status]();
                }
            }
        }
    };

    if (option.type == "POST") {
        xhr.open(option.type, option.url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        xhr.send(option.data);
    } else {
        xhr.open(option.type, option.url+option.data, true);
        xhr.send(null);
    }

}