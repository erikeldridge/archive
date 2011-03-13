$('.routed').click(function(e){
  var controller = e.target.href.split('/').pop();
  if(!window.history || !window.history.pushState){
    return;
  }
  window[controller]();
  e.preventDefault();
});