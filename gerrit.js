$('head')
  .append('<link type="text/css" rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">');

var layout = '\
<div class="container">\
  <div class="navbar">\
    <div class="navbar-inner">\
      <div class="container">\
        <ul class="nav">\
          <li>\
            <a href="#mine">Home</a>\
          </li>\
        </ul>\
      </div>\
    </div>\
  </div>\
  <div class="row">\
    <div class="span12">\
    </div>\
    <div class="span12">\
    sdlfkjdfkljslkjlfjs\
    </div>\
  </div>\
</div>\
';

$('body').append(layout);

$.ajaxSetup({
  type: 'post',
  headers: {
    'Accept': 'application/json,application/json,application/jsonrequest',
    'Content-Type': 'application/json; charset=UTF-8'
  }
});

function gerretXsrfKey(){
  var matches;
  $.each(document.cookie.split(';'), function(i, str){
    matches = str.match(/GerritAccount=(.*)/);
    if(matches) return true;
  });
  return matches[1];
}

function callChangeListService(){
  $.ajax({
    url: '/gerrit/rpc/ChangeListService',
    data: '{"jsonrpc":"2.0","method":"forAccount","params":[{"id":1000128}],"id":1,"xsrfKey":"aScfprr3MHhGNTjBMWnb4lxOqTg.9fg7sW"}',
    success: function(a,s,d){
      console.log(a,s,d);
    }
  });
}

callChangeListService();
