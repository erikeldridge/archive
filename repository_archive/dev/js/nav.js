var routes = {
  '#mine': showDashboard,
  '#q,([^,]+)': showSearchResults,
  '#patch,sidebyside,([^,]+),([^,]+),(.+)': showDiff,
  '#change,(\\d+)([,\\a]+)?': showChangeDetails,
  '#signin': showSignInPage
};
function routeTo(hash){
  for(var route in routes) {
    var matches = hash.match(new RegExp(route));
    if(matches){

      // Update URL.
      history.pushState(null, null, hash);

      // Hide all pages.
      $('.container .page').hide();

      // Route handler is responsible for showing page.
      routes[route].call(this, matches);

      break;
    }
  }
}