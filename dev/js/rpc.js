function callChangeListService(callback){
  $.ajax({
    url: '/gerrit/rpc/ChangeListService',
    data: '{"jsonrpc":"2.0","method":"forAccount","params":[{"id":'+config.currentUser.accountId.id+'}],"xsrfKey":"'+config.xsrfKey+'"}',
    success: callback
  });
}
function getChangeDetails(id, callback){
  $.ajax({
    url: '/gerrit/rpc/ChangeDetailService',
    data: '{"jsonrpc":"2.0","method":"changeDetail","params":[{"id":'+id+'}],"xsrfKey":"'+config.xsrfKey+'"}',
    success: callback
  });
}
function search(query, callback){
  $.ajax({
    url: '/gerrit/rpc/ChangeListService',
    data: '{"jsonrpc":"2.0","method":"allQueryNext","params":["'+query+'","z",25],"xsrfKey":"'+config.xsrfKey+'"}',
    success: function(response){
      callback(response.result);
    }
  });
}
function signIn(username, password, callbacks){
  $.ajax({
    url: '/gerrit/rpc/UserPassAuthService',
    data: '{"jsonrpc":"2.0","method":"authenticate","params":["'+username+'","'+password+'"]}',
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