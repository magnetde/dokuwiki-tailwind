/* DOKUWIKI:include_once js/search.js */
/* DOKUWIKI:include_once js/plugin/copy2clipboard.js */
/* DOKUWIKI:include_once js/plugin/aichat.js */
/* DOKUWIKI:include_once js/plugin/acmenu.js */
/* DOKUWIKI:include_once js/plugin/tabbox.js */
/* DOKUWIKI:include_once js/code-copy-btn.js */

if(JSINFO.ACT === 'edit' || JSINFO.ACT === 'preview') {
    /* DOKUWIKI:include_once js/toolbar.js */
    /* DOKUWIKI:include_once js/draft.js */
}

if(JSINFO.ACT === 'media' || JSINFO.ACT === 'show') { // media manager or media popup
    /* DOKUWIKI:include_once js/media.js */
}

if(JSINFO.ACT === 'admin') {
    /* DOKUWIKI:include_once js/admin-default-toggle.js */
}
