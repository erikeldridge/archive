function showDashboard(){

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
}

function showChangeDetails(matches){

  var id = matches[1];

  getChangeDetails(id, function(response){

    var view = {
      title: response.result.change.subject,
      changeShaId: response.result.change.changeKey.id,
      changeWebId: id,
      changeWebIdSuffix: String(id).substr(-2),
      currentPatchId: response.result.currentDetail.patchSet.id.patchSetId,
      patchCount: response.result.change.nbrPatchSets,
      owner: response.result.currentDetail.info.author.name,
      project: response.result.change.dest.projectName.name,
      uploaded: response.result.change.createdOn,
      updated: response.result.change.lastUpdatedOn,
      status: response.result.change.status,
      message: response.result.currentDetail.info.message,
      reviewers: [],
      patches: []
    };

    var names = mapAccountIdsToNames(response.result.accounts.accounts);
    $.each(response.result.approvals, function(i, reviewer){
      view.reviewers.push({
        name: names[reviewer.account.id]
      });
    });
    $.each(response.result.currentDetail.patches, function(i, patch){
      view.patches.push({
        file: patch.key.fileName,
        changeType: patch.changeType,
        commentCount: patch.nbrComments
      });
    });

    var html = Mustache.render(templates.change, view);

    $('#change').html(html).show();
  });
}

function showSignInPage(){

  $('#sign-in').show();

  $('#sign-in form').unbind().submit(function(){

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

function showSearchResults(matches){

  var query = matches[1];

  if(!authenticated()){
    location.hash = '#signin';
    return;
  }

  search(query, function(results){

    var view = {
      title: 'Search results for '+query,
      changes: []
    };

    var names = mapAccountIdsToNames(results.accounts.accounts);
    $.each(results.changes, function(i, change){
      view.changes.push({
        id: change.id.id,
        key: change.key.id.substr(0,8),
        project: change.project.key.name,
        branch: change.branch,
        owner: names[change.owner.id],
        status: change.status,
        subject: change.subject,
        updated: change.lastUpdatedOn
      });
    });

    var html = Mustache.render(templates.search, view);

    $('#search').html(html).show();

  });
}

function authenticated(){
  return config.xsrfKey && config.currentUser;
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