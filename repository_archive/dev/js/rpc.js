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
function getPatchDetails(fileName, changeId, patchSetId, callback){
  var data = {
    "jsonrpc": "2.0",
    "method": "patchScript",
    "params": [
      {
        "fileName": fileName,
        "patchSetId": {
          "changeId": {
            "id": changeId
          },
          "patchSetId": patchSetId
        }
      },
      null,
      {
        "changeId": {
          "id": changeId
        },
        "patchSetId": patchSetId
      },
      {
        "accountId": {
          "id": config.currentUser.accountId.id
        },
        "context": 10,
        "expandAllComments": false,
        "ignoreWhitespace": "N",
        "intralineDifference": true,
        "lineLength": 100,
        "showTabs": true,
        "showWhitespaceErrors": true,
        "skipDeleted": false,
        "skipUncommented": false,
        "syntaxHighlighting": true,
        "tabSize": 8
      }
    ],
    "id": 5,
    "xsrfKey": config.xsrfKey
  };
  $.ajax({
    url: '/gerrit/rpc/PatchDetailService',
    data: JSON.stringify(data),
    success: function(response){
      callback(response.result);
    }
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