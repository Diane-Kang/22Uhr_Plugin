var waitForEl = function(selector, callback) {
    if (jQuery(selector).length) {
      callback();
    } else {
      setTimeout(function() {
        waitForEl(selector, callback);
      }, 100);
    }
  };

waitForEl('.show_all', function(){
  jQuery('.show_all').click(function(e){
    jQuery('.haupthaus-list').toggleClass('show-only-short-list');
    })
});