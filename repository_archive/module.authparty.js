Y.add( 'authparty', function (Y) {
    
    Y.namespace( 'AuthParty' );
    
    Y.AuthParty.loginComplete = function ( openid, oauthConsumerKey, assertion ) { 

    	// verify assertion
    	var query = Y.String.supplant(
    	    "use 'http://gist.github.com/yql/yql-tables/raw/master/openid/openid.normalize.xml' as openid.normalize;" 
    	    + " use 'http://test.erikeldridge.com/openid-oauth-yql-yui-party/complete.xml' as complete;" 
    	    + " select * from complete where oauthConsumerKey='{key}' and assert='{assert}' and normalizedOpenid in " 
    	    + " ( select id from openid.normalize where id='{id}' )", 
            {
        	    id: openid,
        	    key: oauthConsumerKey,
        	    assert: encodeURIComponent(assertion)
            }
        );

    	new Y.yql(query, function(r) {

    		if (r.error) {
    			Y.log(r);
    			return;
    		}

    		if (r.query.results.success) {
    			Y.fire('authparty:loginSuccess');
    		} else {
    			Y.fire('authparty:loginError');
    		}

    	},
    	{
    	    // incl diagnostic info & disable cache while in development
    		debug: true,
    		diagnostics: true
    	},
    	{
    	    // use https
    		secure: true
    	});
    };
    
    Y.AuthParty.loginStart = function ( uri, popup ) {
        
        // set default args
        popup = popup || true;

    	// open popup for auth
    	var popup = window.open(
    	    uri, 
    	    '', 
    	    'toolbar=0,scrollbars=1,location=1,statusbar=1,menubar=0,resizable=1,width=500,height=500,left=200,top=200'
    	);
    };
    
}, { requires: ['string', 'gallery-yql'] } );