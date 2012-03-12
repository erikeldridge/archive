var routes = {
  '#mine': showDashboard,
  '#q,([^,]+)': showSearchResults,
  '#change,(\\d+)': showChangeDetails,
  '#signin': showSignInPage
};
function routeTo(hash){
  $('.container .page').hide();
  for(route in routes) {
    var matches = hash.match(new RegExp(route));
    if(matches){
      routes[route].call(this, matches);
      break;
    }
  }
}