<!--
/**
 * Source: http://github.com/erikeldridge/iframeio/tree/master
 * Copyright: (c) 2009, Erik Eldridge, all rights reserved
 * License: BSD Open Source License http://gist.github.com/375593
 **/
-->
var iframeio = (function () {
	var requests = {},
	    serverUrl = 'http://localhost/~eldridge/github/erikeldridge/iframeio/server/',
		request = function (params, userCallback) {
			var iframe = document.createElement('iframe'),
				id = null,
				chunks = [],
				collected = 0,
				iframeCallback = function (value) {
					var index = value['index'];
					chunks[index] = value['chunk'];
					collected++;
					
		            //if collected equals total (coercion intended), we're done
		            if (collected == value['total']) {

						//decode and cache data
						var data = decodeURIComponent(chunks.join(''));
						
						//do something w/ data
						userCallback(data);
					
						//clean up
						requests[id] = null;
						document.body.removeChild(iframe);
		            }
				};
				
			//generate a unique identifier for each request
			do {
				var now = new Date().getTime().toString(),
					rand = Math.random().toString().substr(3, 5);
				id = now + rand;
			} while (request[id]);
			
			//build out request params
			var src = serverUrl + '?id=' + id;
			for (var key in params) {
				src += '&' + key + '=' + params[key];
			}
			
			//init iframe channel
			iframe.style.display = 'none';
            iframe.src = src;
			iframe.id = id;
			document.body.appendChild(iframe);
			
			
			return {
				'id' : id,
				'iframeCallback' : iframeCallback
			};
		};

	return {
	    serverUrl: serverUrl,
		'makeRequest' : function (params, userCallback) {
			var req = request(params, userCallback);
			requests[req.id] = req;
		},
		'requests' : requests
	};
}());