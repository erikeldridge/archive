function callChangeListService(callback){
  $.ajax({
    url: '/gerrit/rpc/ChangeListService',
    data: '{"jsonrpc":"2.0","method":"forAccount","params":[{"id":'+config.currentUser.accountId.id+'}],"id":1,"xsrfKey":"'+config.xsrfKey+'"}',
    success: callback
  });
}
function getChangeDetails(id, callback){
  $.ajax({
    url: '/gerrit/rpc/ChangeDetailService',
    data: '{"jsonrpc":"2.0","method":"changeDetail","params":[{"id":'+id+'}],"id":61,"xsrfKey":"'+config.xsrfKey+'"}',
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