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
  var Mustache=(typeof module!=="undefined"&&module.exports)||{};(function(exports){exports.name="mustache.js";exports.version="0.5.0-dev";exports.tags=["{{","}}"];exports.parse=parse;exports.compile=compile;exports.render=render;exports.clearCache=clearCache;exports.to_html=function(template,view,partials,send){var result=render(template,view,partials);if(typeof send==="function"){send(result);}else{return result;}};var _toString=Object.prototype.toString;var _isArray=Array.isArray;var _forEach=Array.prototype.forEach;var _trim=String.prototype.trim;var isArray;if(_isArray){isArray=_isArray;}else{isArray=function(obj){return _toString.call(obj)==="[object Array]";};}var forEach;if(_forEach){forEach=function(obj,callback,scope){return _forEach.call(obj,callback,scope);};}else{forEach=function(obj,callback,scope){for(var i=0,len=obj.length;i<len;++i){callback.call(scope,obj[i],i,obj);}};}var spaceRe=/^\s*$/;function isWhitespace(string){return spaceRe.test(string);}var trim;if(_trim){trim=function(string){return string==null?"":_trim.call(string);};}else{var trimLeft,trimRight;if(isWhitespace("\xA0")){trimLeft=/^\s+/;trimRight=/\s+$/;}else{trimLeft=/^[\s\xA0]+/;trimRight=/[\s\xA0]+$/;}trim=function(string){return string==null?"":String(string).replace(trimLeft,"").replace(trimRight,"");};}var escapeMap={"&":"&amp;","<":"&lt;",">":"&gt;",'"':'&quot;',"'":'&#39;'};function escapeHTML(string){return String(string).replace(/&(?!\w+;)|[<>"']/g,function(s){return escapeMap[s]||s;});}function debug(e,template,line,file){file=file||"<template>";var lines=template.split("\n"),start=Math.max(line-3,0),end=Math.min(lines.length,line+3),context=lines.slice(start,end);var c;for(var i=0,len=context.length;i<len;++i){c=i+start+1;context[i]=(c===line?" >> ":"    ")+context[i];}e.template=template;e.line=line;e.file=file;e.message=[file+":"+line,context.join("\n"),"",e.message].join("\n");return e;}function lookup(name,stack,defaultValue){if(name==="."){return stack[stack.length-1];}var names=name.split(".");var lastIndex=names.length-1;var target=names[lastIndex];var value,context,i=stack.length,j,localStack;while(i){localStack=stack.slice(0);context=stack[--i];j=0;while(j<lastIndex){context=context[names[j++]];if(context==null){break;}localStack.push(context);}if(context&&typeof context==="object"&&target in context){value=context[target];break;}}if(typeof value==="function"){value=value.call(localStack[localStack.length-1]);}if(value==null){return defaultValue;}return value;}function renderSection(name,stack,callback,inverted){var buffer="";var value=lookup(name,stack);if(inverted){if(value==null||value===false||(isArray(value)&&value.length===0)){buffer+=callback();}}else if(isArray(value)){forEach(value,function(value){stack.push(value);buffer+=callback();stack.pop();});}else if(typeof value==="object"){stack.push(value);buffer+=callback();stack.pop();}else if(typeof value==="function"){var scope=stack[stack.length-1];var scopedRender=function(template){return render(template,scope);};buffer+=value.call(scope,callback(),scopedRender)||"";}else if(value){buffer+=callback();}return buffer;}function parse(template,options){options=options||{};var tags=options.tags||exports.tags,openTag=tags[0],closeTag=tags[tags.length-1];var code=['var buffer = "";',"\nvar line = 1;","\ntry {",'\nbuffer += "'];var spaces=[],hasTag=false,nonSpace=false;var stripSpace=function(){if(hasTag&&!nonSpace&&!options.space){while(spaces.length){code.splice(spaces.pop(),1);}}else{spaces=[];}hasTag=false;nonSpace=false;};var sectionStack=[],updateLine,nextOpenTag,nextCloseTag;var setTags=function(source){tags=trim(source).split(/\s+/);nextOpenTag=tags[0];nextCloseTag=tags[tags.length-1];};var includePartial=function(source){code.push('";',updateLine,'\nvar partial = partials["'+trim(source)+'"];','\nif (partial) {','\n  buffer += render(partial,stack[stack.length - 1],partials);','\n}','\nbuffer += "');};var openSection=function(source,inverted){var name=trim(source);if(name===""){throw debug(new Error("Section name may not be empty"),template,line,options.file);}sectionStack.push({name:name,inverted:inverted});code.push('";',updateLine,'\nvar name = "'+name+'";','\nvar callback = (function () {','\n  return function () {','\n    var buffer = "";','\nbuffer += "');};var openInvertedSection=function(source){openSection(source,true);};var closeSection=function(source){var name=trim(source);var openName=sectionStack.length!=0&&sectionStack[sectionStack.length-1].name;if(!openName||name!=openName){throw debug(new Error('Section named "'+name+'" was never opened'),template,line,options.file);}var section=sectionStack.pop();code.push('";','\n    return buffer;','\n  };','\n})();');if(section.inverted){code.push("\nbuffer += renderSection(name,stack,callback,true);");}else{code.push("\nbuffer += renderSection(name,stack,callback);");}code.push('\nbuffer += "');};var sendPlain=function(source){code.push('";',updateLine,'\nbuffer += lookup("'+trim(source)+'",stack,"");','\nbuffer += "');};var sendEscaped=function(source){code.push('";',updateLine,'\nbuffer += escapeHTML(lookup("'+trim(source)+'",stack,""));','\nbuffer += "');};var line=1,c,callback;for(var i=0,len=template.length;i<len;++i){if(template.slice(i,i+openTag.length)===openTag){i+=openTag.length;c=template.substr(i,1);updateLine='\nline = '+line+';';nextOpenTag=openTag;nextCloseTag=closeTag;hasTag=true;switch(c){case"!":i++;callback=null;break;case"=":i++;closeTag="="+closeTag;callback=setTags;break;case">":i++;callback=includePartial;break;case"#":i++;callback=openSection;break;case"^":i++;callback=openInvertedSection;break;case"/":i++;callback=closeSection;break;case"{":closeTag="}"+closeTag;case"&":i++;nonSpace=true;callback=sendPlain;break;default:nonSpace=true;callback=sendEscaped;}var end=template.indexOf(closeTag,i);if(end===-1){throw debug(new Error('Tag "'+openTag+'" was not closed properly'),template,line,options.file);}var source=template.substring(i,end);if(callback){callback(source);}var n=0;while(~(n=source.indexOf("\n",n))){line++;n++;}i=end+closeTag.length-1;openTag=nextOpenTag;closeTag=nextCloseTag;}else{c=template.substr(i,1);switch(c){case'"':case"\\":nonSpace=true;code.push("\\"+c);break;case"\r":break;case"\n":spaces.push(code.length);code.push("\\n");stripSpace();line++;break;default:if(isWhitespace(c)){spaces.push(code.length);}else{nonSpace=true;}code.push(c);}}}if(sectionStack.length!=0){throw debug(new Error('Section "'+sectionStack[sectionStack.length-1].name+'" was not closed properly'),template,line,options.file);}stripSpace();code.push('";',"\nreturn buffer;","\n} catch (e) { throw {error: e, line: line}; }");var body=code.join("").replace(/buffer \+= "";\n/g,"");if(options.debug){if(typeof console!="undefined"&&console.log){console.log(body);}else if(typeof print==="function"){print(body);}}return body;}function _compile(template,options){var args="view,partials,stack,lookup,escapeHTML,renderSection,render";var body=parse(template,options);var fn=new Function(args,body);return function(view,partials){partials=partials||{};var stack=[view];try{return fn(view,partials,stack,lookup,escapeHTML,renderSection,render);}catch(e){throw debug(e.error,template,e.line,options.file);}};}var _cache={};function clearCache(){_cache={};}function compile(template,options){options=options||{};if(options.cache!==false){if(!_cache[template]){_cache[template]=_compile(template,options);}return _cache[template];}return _compile(template,options);}function render(template,view,partials){return compile(template)(view,partials);}})(Mustache);

  // jquery cookie
  // https://raw.github.com/carhartl/jquery-cookie/faa09dc38bd3c791212e8fca67ee661af55fa530/jquery.cookie.js
  if(!$.cookie)(function($){$.cookie=function(a,b,c){if(arguments.length>1&&(!/Object/.test(Object.prototype.toString.call(b))||b===null||b===undefined)){c=$.extend({},c);if(b===null||b===undefined){c.expires=-1}if(typeof c.expires==='number'){var d=c.expires,t=c.expires=new Date();t.setDate(t.getDate()+d)}b=String(b);return(document.cookie=[encodeURIComponent(a),'=',c.raw?b:encodeURIComponent(b),c.expires?'; expires='+c.expires.toUTCString():'',c.path?'; path='+c.path:'',c.domain?'; domain='+c.domain:'',c.secure?'; secure':''].join(''))}c=b||{};var e=c.raw?function(s){return s}:decodeURIComponent;var f=document.cookie.split('; ');for(var i=0,pair;pair=f[i]&&f[i].split('=');i++){if(e(pair[0])===a)return e(pair[1]||'')}return null}})(jQuery);

  /* ========== Template ========== */

  var template = '\
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
    <!-- start mine page -->\
    <div class="row page" id="mine" style="display:none">\
      <div class="span12">\
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
      </div>\
    </div>\
    <!-- end mine page -->\
    <!-- start sign in page -->\
    <div class="row page" id="sign-in" style="display:none">\
      <div class="span12">\
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
    </div>\
    <!-- end sign in page -->\
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

  /* ========== RPC wrappers ========== */

  function callChangeListService(callback){
    $.ajax({
      url: '/gerrit/rpc/ChangeListService',
      data: '{"jsonrpc":"2.0","method":"forAccount","params":[{"id":'+config.currentUser.id+'}],"id":1,"xsrfKey":"'+config.xsrfKey+'"}',
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
      success: function(data){
        if(data.result.success){
          callbacks.success(data);
        }else{
          callbacks.error();
        }
      }
    });
  }
  function getAccountInfo(callback){
    $.ajax({
      url: '/gerrit/rpc/AccountSecurity',
      data: '{"jsonrpc":"2.0","method":"myExternalIds","params":[],"id":1,"xsrfKey":"'+config.xsrfKey+'"}',
      success: callback
    });
  }

  /* ========== Nav ========== */

  var routes = {
    '#mine': function(){

      if(!authenticated()){
        location.hash = '#signin';
        return;
      }

      callChangeListService(function(data){

        config.accounts = mapAccountIdsToNames(data.result.accounts.accounts);

        console.log('accounts', config.accounts);

        var view = {
          'outbound': formatChangeListDataForView(data.result.byOwner),
          'inbound': formatChangeListDataForView(data.result.forReview)
        };
        var html = Mustache.render(template, view);

        $('#mine').show();

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
      // debugger
    },
    '#change,[\d]+': function(){
      // debugger
    },
    '#signin': function(){

      $('#sign-in').show();

      $('#sign-in form').submit(function(){

        var username = $(this).find('input[name=username]').val();
        var password = $(this).find('input[name=password]').val();

        signIn(username, password, {
          success: function(){

            config.xsrfKey = $.cookie('GerritAccount');

            getAccountInfo(function(data){

              config.currentUser = {
                id: data.result[0].accountId.id,
                email: data.result[0].emailAddress
              };

              location.hash = '#mine';
            });

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
  var html = Mustache.render(template);
  $('body').append(html);

  // Attach nav
  window.onhashchange = function(event) {
    routeTo(document.location.hash);
  };

  routeTo('#mine');

})();