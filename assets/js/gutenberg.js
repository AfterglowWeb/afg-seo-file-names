/**
* @since 0.9.0
*/
const getPostId = () => wp.data.select('core/editor').getCurrentPostId();
const getPostTitle = () => wp.data.select('core/editor').getEditedPostAttribute('title');
const getPostSlug = () => wp.data.select('core/editor').getEditedPostSlug();
const getPostCat = () => wp.data.select('core/editor').getEditedPostAttribute('categories');
const getPostTag = () => wp.data.select('core/editor').getEditedPostAttribute('tags');
const getPostType = () => wp.data.select('core/editor').getEditedPostAttribute('type');
const getPostAuthor = () => wp.data.select('core/editor').getCurrentPostAttribute('author');

let postId = getPostId();
let title = getPostTitle();
let slug = getPostSlug();
let cat = getPostCat();
let tag = getPostTag();
let type = getPostType();
let authorId = getPostAuthor();

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
        if(sanitizedNewPostId = asf_sanitize.sanitizeInt(newPostId)) {
            datas.id = sanitizedNewPostId;
            asf_ajax(datas);
        }
    }
    postId = newPostId;

    const newTitle = getPostTitle();    
    if( title !== newTitle  && asf_sanitize.sanitizeText(newTitle) ) {
        if(sanitizedNewTitle = asf_sanitize.sanitizeText(newTitle)) {
            datas.title = sanitizedNewTitle;
            asf_ajax(datas);
        }   
    }
    title = newTitle;

    const newSlug = getPostSlug(); 
    if( slug !== newSlug && asf_sanitize.sanitizeText(newSlug)  ) {
        if(sanitizedNewSlug = asf_sanitize.sanitizeText(newSlug)) {
            datas.slug = sanitizedNewSlug;
            asf_ajax(datas);
        }
    }
    slug = newSlug;

    const newCat = getPostCat();    
    if( cat !== newCat && asf_sanitize.sanitizeIds(newCat) ) {
        if(sanitizedNewCat = asf_sanitize.sanitizeIds(newCat)) {
            datas.cat = sanitizedNewCat;
            asf_ajax(datas);
        }
    }
    cat = newCat;

    const newTag = getPostTag();    
    if( tag !== newTag && asf_sanitize.sanitizeIds(newTag) ) {
        if(sanitizedNewTag = asf_sanitize.sanitizeIds(newTag)) {
            datas.tag = sanitizedNewTag;
            asf_ajax(datas);
        }
    }
    tag = newTag;

    const newType = getPostType();    
    if( type !== newType && asf_sanitize.sanitizeText(newType) ) {
        if(sanitizedNewType = asf_sanitize.sanitizeText(newType)) {
            datas.type = sanitizedNewType;
            asf_ajax(datas);
        }
    }
    type = newType;

    const newAuthorId = getPostAuthor();
    if( authorId !== newAuthorId && asf_sanitize.sanitizeInt(newAuthorId) ) {
        if(sanitizedNewAuthorId = asf_sanitize.sanitizeInt(newAuthorId) ) {
            datas.author = sanitizedNewAuthorId;
            asf_ajax(datas);
        }
    }
    authorId = newAuthorId;
}