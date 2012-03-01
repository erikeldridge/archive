$('head')
  .append('<link type="text/css" rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">');

var layout = '\
<div class="container">\
  <div class="row">\
    <div class="span12">\
      <div class="navbar">\
        <div class="navbar-inner">\
          <div class="container">\
            <ul class="nav">\
              <li class="active">\
                <a href="#">Home</a>\
              </li>\
              <li class="dropdown">\
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Account<b class="caret"></b></a>\
                <ul class="dropdown-menu">\
                ...lsdfjsdjfsdljlf\
                </ul>\
              </li>\
              <li><a href="#">Admin</a></li>\
            </ul>\
          </div>\
        </div>\
      </div>\
    </div>\
    <div class="span12">\
    sdlfkjdfkljslkjlfjs\
    </div>\
  </div>\
</div>\
';

$('body').append(layout);

// load jquery and dropdown plugin in order
var script = document.createElement('script');

script.setAttribute('src', 'http://twitter.github.com/bootstrap/assets/js/jquery.js');
document.body.appendChild(script);

script = document.createElement('script');
script.setAttribute('src', 'http://twitter.github.com/bootstrap/assets/js/bootstrap-dropdown.js');
document.body.appendChild(script);
