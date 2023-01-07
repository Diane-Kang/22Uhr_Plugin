
function show_all_list_callback(){

}

var waitForEl = function(selector, callback) {
    if (jQuery(selector).length) {
      callback();
      console.log("callback");
    } else {
      setTimeout(function() {
        waitForEl(selector, callback);
      }, 100);
    }
  };

waitForEl('.show_all', function(){
    jQuery('.show_all').click(function(e){
        console.log("hiere?");
        jQuery('.haupthaus-list').toggleClass('show-only-short-list');
    })
});