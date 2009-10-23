var sdk = function () {
	var hash,
		requests = {},
		request = function (params, userCallback) {
			var iframe = document.createElement('iframe'),
				id = null,
				url = 'http://localhost/~eldridge/foxbat/service/?id=',
				total = null,
				chunks = '',
				collected,
				iframeCallback = function (key, val) {
					switch (key) {
						case 'total':
							total = val;
							collected = 0;
				            chunks = '';
							break;
						case 'chunk':
				            chunks += val;
				            collected++;

				            //if collected equals total (coercion intended), we're done
				            if (collected == total) {

								//decode and cache data
								var data = decodeURIComponent(chunks);
								
								//do something w/ data
								userCallback(data);
							
								//clean up
								requests[id] = null;
								total = null;
								collected = null;
								chunks = null;
								document.body.removeChild(iframe);
				            }
							break;
						default:
						
							//invalid key
							break;
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
			iframe.src = 'http://localhost/~eldridge/foxbat/client/iframe.html';
			iframe.id = id;
			document.body.appendChild(iframe);
			
			//get hash from ui
			if (!hash) {
				var scriptNodes = document.getElementsByTagName('script');
				for (var i = 0; i < scriptNodes.length; i++) {
					if (-1 !== scriptNodes[i].src.indexOf('service/library.js')) {
						hash = scriptNodes[i].innerHTML;
					}
				}
				if (!hash) {
					throw('library js, request() fn, hash must be defined');
				}
			}
			
			//build out request params
			url += '&hash='+hash+'&id='+id;
			for (var key in params) {
				url += '&'+key+'='+params[key];
			}
			iframe.src = url;
			//console.log(iframe);
			//expose methods
			return {
				'id' : id,
				'iframeCallback' : iframeCallback
			};
		};

	return {
		'makeRequest' : function (params, userCallback) {
			var req = request(params, userCallback);
			requests[req.id] = req;//console.log(requests);
		},
		'requests' : requests
	};
}();