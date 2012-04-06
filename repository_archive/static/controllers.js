function github(){

  var data;
  var template;
  var html;
  
  $.ajax({
    url: provider+"/static/timeline_github.json",
    success: function(o){
      data = {
        "timeline": o
      };
      $("body").trigger("ping");
    }
  });
  
  $.ajax({
    url: provider+"/static/partial_timeline.mustache",
    success: function(o){
      template = o;
      $("body").trigger("ping");
    }
  });
  
  $("body").bind("ping", function(){
    if(data && template){
      html = Mustache.to_html(template, data);
      $(this).unbind("ping");
      $(html).replaceAll("#timeline");
    }
  });
  
  window.history.pushState({}, "github", "github");
}

function yahoo(){
  
  var data;
  var template;
  var html;
  
  $.ajax({
    url: provider+"/static/timeline_yahoo.json",
    success: function(o){
      data = {
        "timeline": o
      };
      $("body").trigger("ping");
    }
  });
  
  $.ajax({
    url: provider+"/static/partial_timeline.mustache",
    success: function(o){
      template = o;
      $("body").trigger("ping");
    }
  });
  
  $("body").bind("ping", function(){
    if(data && template){
      html = Mustache.to_html(template, data);
      $(this).unbind("ping");
      $(html).replaceAll("#timeline");
    }
  });
  
  window.history.pushState({}, "yahoo", "yahoo");
}