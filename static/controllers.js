function github(){

  var data;
  var template;
  var html;
  
  rpc.request({
      url: provider+"/static/timeline_yahoo.json",
      method: "GET"
  }, function(response){
      data = {
        "timeline": JSON.parse(response.data)
      };
      if(template && !html){
        html = Mustache.to_html(template, data);
        $("body").trigger("htmlReady");
      }
  });
  
  rpc.request({
      url: provider+"/static/partial_timeline.mustache",
      method: "GET"
  }, function(response){
      template = response.data;
      if(data && !html){
        html = Mustache.to_html(template, data);
        $("body").trigger("htmlReady");
      }
  });
  
  $("body").bind("htmlReady", function(){
    $(this).unbind("htmlReady");
    $(html).replaceAll("#timeline");
  });
  
  window.history.pushState({}, "github", "github");
}

function yahoo(){
  
  var data;
  var template;
  var html;
  
  rpc.request({
      url: provider+"/static/timeline_yahoo.json",
      method: "GET"
  }, function(response){
      data = {
        "timeline": JSON.parse(response.data)
      };
      if(template && !html){
        html = Mustache.to_html(template, data);
        $("body").trigger("htmlReady");
      }
  });
  
  rpc.request({
      url: provider+"/static/partial_timeline.mustache",
      method: "GET"
  }, function(response){
      template = response.data;
      if(data && !html){
        html = Mustache.to_html(template, data);
        $("body").trigger("htmlReady");
      }
  });
  
  $("body").bind("htmlReady", function(){
    $(this).unbind("htmlReady");
    $(html).replaceAll("#timeline");
  });
  
  window.history.pushState({}, "yahoo", "yahoo");
}