(function(){

  /* ========== Config ========== */

  // xhr
  $.ajaxSetup({
    type: 'post',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json; charset=UTF-8'
    }
  });

  // state
  var config = {};

  /* ========== Lib ========== */

  // Mustache
  var Mustache=(typeof module!=="undefined"&&module.exports)||{};(function(w){w.name="mustache.js";w.version="0.5.0-dev";w.tags=["{{","}}"];w.parse=m;w.compile=e;w.render=v;w.clearCache=u;w.to_html=function(A,y,z,B){var x=v(A,y,z);if(typeof B==="function"){B(x)}else{return x}};var s=Object.prototype.toString;var f=Array.isArray;var b=Array.prototype.forEach;var g=String.prototype.trim;var i;if(f){i=f}else{i=function(x){return s.call(x)==="[object Array]"}}var r;if(b){r=function(y,z,x){return b.call(y,z,x)}}else{r=function(A,B,z){for(var y=0,x=A.length;y<x;++y){B.call(z,A[y],y,A)}}}var k=/^\s*$/;function c(x){return k.test(x)}var p;if(g){p=function(x){return x==null?"":g.call(x)}}else{var n,h;if(c("\xA0")){n=/^\s+/;h=/\s+$/}else{n=/^[\s\xA0]+/;h=/[\s\xA0]+$/}p=function(x){return x==null?"":String(x).replace(n,"").replace(h,"")}}var d={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"};function o(x){return String(x).replace(/&(?!\w+;)|[<>"']/g,function(y){return d[y]||y})}function l(D,F,G,z){z=z||"<template>";var H=F.split("\n"),x=Math.max(G-3,0),A=Math.min(H.length,G+3),y=H.slice(x,A);var E;for(var B=0,C=y.length;B<C;++B){E=B+x+1;y[B]=(E===G?" >> ":"    ")+y[B]}D.template=F;D.line=G;D.file=z;D.message=[z+":"+G,y.join("\n"),"",D.message].join("\n");return D}function t(x,F,E){if(x==="."){return F[F.length-1]}var D=x.split(".");var B=D.length-1;var C=D[B];var G,y,A=F.length,z,H;while(A){H=F.slice(0);y=F[--A];z=0;while(z<B){y=y[D[z++]];if(y==null){break}H.push(y)}if(y&&typeof y==="object"&&C in y){G=y[C];break}}if(typeof G==="function"){G=G.call(H[H.length-1])}if(G==null){return E}return G}function j(A,x,E,z){var y="";var C=t(A,x);if(z){if(C==null||C===false||(i(C)&&C.length===0)){y+=E()}}else{if(i(C)){r(C,function(F){x.push(F);y+=E();x.pop()})}else{if(typeof C==="object"){x.push(C);y+=E();x.pop()}else{if(typeof C==="function"){var B=x[x.length-1];var D=function(F){return v(F,B)};y+=C.call(B,E(),D)||""}else{if(C){y+=E()}}}}}return y}function m(Z,B){B=B||{};var K=B.tags||w.tags,L=K[0],G=K[K.length-1];var y=['var buffer = "";',"\nvar line = 1;","\ntry {",'\nbuffer += "'];var F=[],aa=false,X=false;var V=function(){if(aa&&!X&&!B.space){while(F.length){y.splice(F.pop(),1)}}else{F=[]}aa=false;X=false};var S=[],P,C,M;var U=function(ab){K=p(ab).split(/\s+/);C=K[0];M=K[K.length-1]};var J=function(ab){y.push('";',P,'\nvar partial = partials["'+p(ab)+'"];',"\nif (partial) {","\n  buffer += render(partial,stack[stack.length - 1],partials);","\n}",'\nbuffer += "')};var x=function(ad,ab){var ac=p(ad);if(ac===""){throw l(new Error("Section name may not be empty"),Z,I,B.file)}S.push({name:ac,inverted:ab});y.push('";',P,'\nvar name = "'+ac+'";',"\nvar callback = (function () {","\n  return function () {",'\n    var buffer = "";','\nbuffer += "')};var E=function(ab){x(ab,true)};var T=function(ac){var ab=p(ac);var ae=S.length!=0&&S[S.length-1].name;if(!ae||ab!=ae){throw l(new Error('Section named "'+ab+'" was never opened'),Z,I,B.file)}var ad=S.pop();y.push('";',"\n    return buffer;","\n  };","\n})();");if(ad.inverted){y.push("\nbuffer += renderSection(name,stack,callback,true);")}else{y.push("\nbuffer += renderSection(name,stack,callback);")}y.push('\nbuffer += "')};var W=function(ab){y.push('";',P,'\nbuffer += lookup("'+p(ab)+'",stack,"");','\nbuffer += "')};var z=function(ab){y.push('";',P,'\nbuffer += escapeHTML(lookup("'+p(ab)+'",stack,""));','\nbuffer += "')};var I=1,Y,D;for(var Q=0,R=Z.length;Q<R;++Q){if(Z.slice(Q,Q+L.length)===L){Q+=L.length;Y=Z.substr(Q,1);P="\nline = "+I+";";C=L;M=G;aa=true;switch(Y){case"!":Q++;D=null;break;case"=":Q++;G="="+G;D=U;break;case">":Q++;D=J;break;case"#":Q++;D=x;break;case"^":Q++;D=E;break;case"/":Q++;D=T;break;case"{":G="}"+G;case"&":Q++;X=true;D=W;break;default:X=true;D=z}var A=Z.indexOf(G,Q);if(A===-1){throw l(new Error('Tag "'+L+'" was not closed properly'),Z,I,B.file)}var O=Z.substring(Q,A);if(D){D(O)}var N=0;while(~(N=O.indexOf("\n",N))){I++;N++}Q=A+G.length-1;L=C;G=M}else{Y=Z.substr(Q,1);switch(Y){case'"':case"\\":X=true;y.push("\\"+Y);break;case"\r":break;case"\n":F.push(y.length);y.push("\\n");V();I++;break;default:if(c(Y)){F.push(y.length)}else{X=true}y.push(Y)}}}if(S.length!=0){throw l(new Error('Section "'+S[S.length-1].name+'" was not closed properly'),Z,I,B.file)}V();y.push('";',"\nreturn buffer;","\n} catch (e) { throw {error: e, line: line}; }");var H=y.join("").replace(/buffer \+= "";\n/g,"");if(B.debug){if(typeof console!="undefined"&&console.log){console.log(H)}else{if(typeof print==="function"){print(H)}}}return H}function q(B,z){var y="view,partials,stack,lookup,escapeHTML,renderSection,render";var x=m(B,z);var A=new Function(y,x);return function(D,E){E=E||{};var C=[D];try{return A(D,E,C,t,o,j,v)}catch(F){throw l(F.error,B,F.line,z.file)}}}var a={};function u(){a={}}function e(y,x){x=x||{};if(x.cache!==false){if(!a[y]){a[y]=q(y,x)}return a[y]}return q(y,x)}function v(z,x,y){return e(z)(x,y)}})(Mustache);

  // jquery cookie
  // https://raw.github.com/carhartl/jquery-cookie/faa09dc38bd3c791212e8fca67ee661af55fa530/jquery.cookie.js
  if(!$.cookie)(function($){$.cookie=function(a,b,c){if(arguments.length>1&&(!/Object/.test(Object.prototype.toString.call(b))||b===null||b===undefined)){c=$.extend({},c);if(b===null||b===undefined){c.expires=-1}if(typeof c.expires==='number'){var d=c.expires,t=c.expires=new Date();t.setDate(t.getDate()+d)}b=String(b);return(document.cookie=[encodeURIComponent(a),'=',c.raw?b:encodeURIComponent(b),c.expires?'; expires='+c.expires.toUTCString():'',c.path?'; path='+c.path:'',c.domain?'; domain='+c.domain:'',c.secure?'; secure':''].join(''))}c=b||{};var e=c.raw?function(s){return s}:decodeURIComponent;var f=document.cookie.split('; ');for(var i=0,pair;pair=f[i]&&f[i].split('=');i++){if(e(pair[0])===a)return e(pair[1]||'')}return null}})(jQuery);

  /* ========== Template ========== */

  var templates = {};
  templates.app = '\
  <div class="container" style="">\
    <!-- start nav -->\
    <div class="navbar">\
      <div class="navbar-inner">\
        <div class="container">\
          <ul class="nav">\
            <a class="brand" href="#mine">\
              Gerrit\
            </a>\
          </ul>\
        </div>\
      </div>\
    </div>\
    <!-- end nav -->\
    <div class="row pages">\
      <!-- start mine page -->\
      <div class="span12 page" id="mine">\
      </div>\
      <!-- end mine page -->\
      <!-- start sign in page -->\
      <div class="span12 page" id="sign-in" style="display:none">\
        <form class="form-horizontal">\
          <fieldset>\
            <legend>Sign in</legend>\
            <div class="control-group">\
              <label class="control-label" for="username">Username</label>\
              <div class="controls">\
                <input type="text" class="span3" placeholder="Username" name="username">\
              </div>\
            </div>\
            <div class="control-group">\
              <label class="control-label" for="password">Password</label>\
              <div class="controls">\
                <input type="password" class="span3" placeholder="Password" name="password">\
              </div>\
            </div>\
            <div class="form-actions">\
              <button type="submit" class="btn btn-primary">Submit</button>\
            </div>\
          </fieldset>\
        </form>\
      </div>\
      <!-- end sign in page -->\
    </div>\
    <div class="row">\
      <div class="span12">\
        <footer class="footer">\
        <hr>\
        <p><a href="https://github.com/erikeldridge/gerrit.js">Gerrit.js</a></p>\
        </footer>\
      </div>\
    </div>\
  </div>\
  ';
  templates.mine = '\
  <h2>Inbound</h2>\
  <table class="table table-striped table-bordered">\
    <thead>\
      <tr>\
        <th>ID</th>\
        <th>Subject</th>\
        <th>Updated</th>\
        <th>Owner</th>\
      </tr>\
    </thead>\
    <tbody>\
      {{#inbound}} \
      <tr>\
        <td>{{key}}</td>\
        <td><a href="/{{id}}">{{subject}}</a></td>\
        <td>{{updated}}</td>\
        <td>{{owner}}</td>\
      </tr>\
      {{/inbound}} \
    </tbody>\
  </table>\
  <h2>Outbound</h2>\
  <table class="table table-striped table-bordered">\
    <thead>\
      <tr>\
        <th>ID</th>\
        <th>Subject</th>\
        <th>Updated</th>\
      </tr>\
    </thead>\
    <tbody>\
      {{#outbound}} \
      <tr>\
        <td>{{key}}</td>\
        <td><a href="/{{id}}">{{subject}}</a></td>\
        <td>{{updated}}</td>\
      </tr>\
      {{/outbound}} \
    </tbody>\
  </table>\
  ';
  /* ========== RPC wrappers ========== */

  function callChangeListService(callback){
    $.ajax({
      url: '/gerrit/rpc/ChangeListService',
      data: '{"jsonrpc":"2.0","method":"forAccount","params":[{"id":'+config.currentUser.accountId.id+'}],"id":1,"xsrfKey":"'+config.xsrfKey+'"}',
      success: callback
    });
  }
  function signIn(username, password, callbacks){
    $.ajax({
      url: '/gerrit/rpc/UserPassAuthService',
      data: '{"jsonrpc":"2.0","method":"authenticate","params":["'+username+'","'+password+'"],"id":2}',
      error: function(jqXHR, textStatus, errorThrown){
        console.log('signIn request failure', jqXHR, textStatus, errorThrown);
      },
      success: function(data, textStatus, jqXHR){
        if(data.result.success){
          callbacks.success(data, textStatus, jqXHR);
        }else{
          callbacks.error();
        }
      }
    });
  }

  /* ========== Nav ========== */

  var routes = {
    '#mine': function(){

      if(!authenticated()){
        document.location.hash = '#signin';
        return;
      }

      callChangeListService(function(data){

        config.accounts = mapAccountIdsToNames(data.result.accounts.accounts);

        console.log('accounts', config.accounts);

        var view = {
          'outbound': formatChangeListDataForView(data.result.byOwner),
          'inbound': formatChangeListDataForView(data.result.forReview)
        };
        var html = Mustache.render(templates.mine, view);

        $('#mine').html(html).show();

        console.log('changes', data);
      });

      function formatChangeListDataForView(data){
        var formatted = [];
        $.each(data, function(i, row){
          var updatedDate = new Date(row.lastUpdatedOn);
          formatted.push({
            id: row.id.id,
            key: row.key.id.substr(0,8),
            subject: row.subject,
            updated: updatedDate.getMonth() + 1 + '/' + updatedDate.getDate(),
            owner: config.accounts[row.owner.id]
          });
        });
        return formatted;
      }
      function mapAccountIdsToNames(accounts){
        var map = {};
        $.each(accounts, function(i, account){
          if(i % 2 == 0){
            return 'continue';
          }
          map[account.id.id] = account.fullName;
        });
        return map;
      }
    },
    '#change$': function(){
    },
    '#change,[\d]+': function(){
    },
    '#signin': function(){

      $('#sign-in').show();

      $('#sign-in form').submit(function(){

        var username = $(this).find('input[name=username]').val();
        var password = $(this).find('input[name=password]').val();

        signIn(username, password, {
          success: function(){

            // Hard refresh to force re-init w/ account cookie set
            document.location = '/';

          },
          error: function(){
            console.log('signIn input failure');
          }
        });
        return false;
      });
    }
  };
  function routeTo(hash){
    $('.container .page').hide();
    $.each(routes, function(regex, action){
      var matches = hash.match(new RegExp(regex));
      if(matches){
        action.call(matches);
      }
    });
  }
  function authenticated(){
    return config.xsrfKey && config.currentUser;
  }

  /* ========== Init ========== */

  // Remove gerrit UI
  $('body').empty();

  // Import bootstrap
  $('head').append('<link type="text/css" rel="stylesheet" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css">');

  // Apply template
  var html = Mustache.render(templates.app);
  $('body').append(html);

  // Attach nav
  window.onhashchange = function(event) {
    routeTo(document.location.hash);
  };

  // Scrape user info, and cache xsrf token, so we can make requests
  var text = $('script').first().text();
  if(/gerrit_hostpagedata/.test(text)){
    var json = text.replace(/var\sgerrit_hostpagedata=/,'').replace(/;/g, '').split(/gerrit_hostpagedata\.\w+=/)[1];
    config.currentUser = JSON.parse(json);

    console.log('user', config.currentUser);
  }
  config.xsrfKey = $.cookie('GerritAccount');

  // Default start view
  routeTo('#mine');

})();