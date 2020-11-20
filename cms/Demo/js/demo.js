function donut_piece_click ()
{
    const pieces = document.getElementsByClassName('donut-piece');
    let length   = pieces.length;

    // clean up old selection
    for (let i = 0; i < length; ++i) {
        jsOMS.removeClass(pieces[i], 'active');
    }

    // new selection
    jsOMS.addClass(document.getElementById('sagittarius'), 'hidden');
    jsOMS.removeClass(document.getElementById('dwf-element-logo'), 'hidden');
    jsOMS.addClass(this, 'active');
};

jsOMS.ready(function ()
{
    "use strict";

    const pieces = document.getElementsByClassName('donut-piece');
    let length   = pieces.length;

    for (let i = 0; i < length; ++i) {
        pieces[i].addEventListener('click', donut_piece_click);
    }
});
