var sdk = function () {
	var requests = {},
		request = function (params, userCallback) {
			var iframe = document.createElement('iframe'),
				id = null,
				url = 'http://localhost/~eldridge/foxbat/server/?id=',
				chunks = [],
				collected = 0,
				iframeCallback = function (value) {
					var index = value['index'];
					chunks[index] = value['chunk'];
					collected++;
		            //if collected equals total (coercion intended), we're done
		            if (collected == value['total']) {

						//decode and cache data
						var data = decodeURIComponent(chunks);
						
						//do something w/ data
						userCallback(data);
					
						//clean up
						requests[id] = null;
						// collected = 0;
						// 						chunks = [];
						document.body.removeChild(iframe);
		            }
				};
				
			//generate a unique identifier for each request
			do {
				var now = new Date().getTime().toString(),
					rand = Math.random().toString().substr(3, 5);
				id = now + rand;
			} while (request[id]);
			
			//init iframe channel
			iframe.style.display = 'none';
			iframe.src = 'iframe.html';
			iframe.id = id;
			document.body.appendChild(iframe);
			
			//build out request params
			url += id;
			for (var key in params) {
				url += '&'+key+'='+params[key];
			}
			iframe.src = url;
			
			return {
				'id' : id,
				'iframeCallback' : iframeCallback
			};
		};

	return {
		'makeRequest' : function (params, userCallback) {
			var req = request(params, userCallback);
			requests[req.id] = req;
		},
		'requests' : requests
	};
}();