// MAKE: TARGET: app/code/Nwdthemes/Revslider/view/frontend/web/public/assets/js/jquery.themepunch.revolution.min.js

if (typeof punchgs === 'undefined') {
    jQuery('.rev_slider')
        .html("Error: 'themepunch.gs.min.js' was not loaded! Please make sure 'revslider.head.includes' block is added to 'after.body.start' block or change layout to other reference block in 'nwdthemes_revslider_default.xml'.")
        .css({color: 'red'})
        .show();
} else {

    (function(jQuery,undefined){
        // MAKE: SOURCE: app/code/Nwdthemes/Revslider/view/adminhtml/web/public/assets/js/source/jquery.themepunch.revolution.js
        // MAKE: MODIFY: Remove module define wrap!
    })(jQuery);

    // MAKE: SOURCE: app/code/Nwdthemes/Revslider/view/adminhtml/web/public/assets/js/tools/TouchSwipe.js

    var nwdjQuery = jQuery;
}

// MAKE: MINIFY