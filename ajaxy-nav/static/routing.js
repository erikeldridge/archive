$('body').delegate('.routed', 'click', function(e){
  // http://mislav.uniqpath.com/2011/03/click-hijack/
  if(e.which != 1 || e.metaKey || e.shiftKey){
    return;
  }
  if(!window.history || !window.history.pushState){
    return;
  }
  var controller = e.target.href.split('/').pop();
  window[controller]();
  return false;
});