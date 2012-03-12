// Remove gerrit UI
$('body').empty();

// Import bootstrap using local css for performance
$style = $('<style type="text/css"></style>').html(bootstrap);
$('head').append($style);

// Apply template
var html = Mustache.render(templates.app);
$('body').append(html);

// Attach nav
window.onhashchange = function(event) {
  routeTo(document.location.hash);
};

// Attach search box handling
$('.navbar form').submit(function(){
  var q = $(this).find('input[name=q]').val();
  var hash = '#q,'+q;
  history.pushState(null, null, hash);
  routeTo(hash);
  return false;
});

// Scrape user info, and cache xsrf token, so we can make requests
var text = $('script').first().text();
if(/gerrit_hostpagedata/.test(text)){
  var json = text.replace(/var\sgerrit_hostpagedata=/,'').replace(/;/g, '').split(/gerrit_hostpagedata\.\w+=/)[1];
  config.currentUser = JSON.parse(json);

  console.log('user', config.currentUser);
}
config.xsrfKey = $.cookie('GerritAccount');

if(location.hash){
  routeTo(location.hash);
}else{

  // Default start view
  location.hash = '#mine';
}