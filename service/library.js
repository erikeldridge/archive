//todo: look into using a standard js lib implementation of iframe comm util
var sdk = (function () {
	
		var totalQtyChunks = null,
			qtyChunksCollected = null,
			chunkStr = null,
			iframes = [],
			hash = null,
			crumb = null,	
			requestQueue = [],		
			completeCallback = function (data) {
				//This is a stub fn.  
				//The user should define a callback as a param to request()
			},
		
			//todo: make iframe callback name dynamic
		    iframeCallback = function (chunk) {

		        //validate input
		        if('string' !== typeof chunk){
		           return;
		        }

		        //Count defined by 1st iframe.
		        //Guard against chunkStr w/ 'totalQtyChunks' str in them.
		        //initialize affiliate vars here so we can reuse
		        if(!totalQtyChunks && -1 !== chunk.indexOf('totalQtyChunks')){
		            totalQtyChunks = Number(chunk.substr(chunk.indexOf('=') + 1));
		            qtyChunksCollected = 0;
		            chunkStr = '';

		        //Chunks are passed to subsequent iframes.
		        }else if (totalQtyChunks) {
		            chunkStr += chunk;
		            qtyChunksCollected++;

		            //if qtyChunksCollected === totalQtyChunks, we're done
		            if (qtyChunksCollected === totalQtyChunks) {

						//decode and cache data
						var data = decodeURIComponent(chunkStr);

						//do something w/ data
						completeCallback(data);

						//clear affiliate vars for next use
						totalQtyChunks = qtyChunksCollected = chunkStr = null;
		            }
		        }
		    },
			initCallback = function () {
				
			},
			request = function (paramsObj, fn) {
				
				if (!hash) {
					throw('hash required for all requests');
				}
				
				//todo: allow global def of service url
				//every request requires hash so server can lookup acct
				var src = 'service/?hash=' + hash;
				
				if (paramsObj) {
					
					if (!crumb) {
						
						//add request to init callback
						console.log('adding req to queue');
						requestQueue.push({'params':paramsObj, 'callback':fn});
						return;
					}
					
					src += '&crumb=' + crumb;
					
					//append params to req url
					//todo: either use foreach or add hasOwnObject check js-good-stuff-style
					for (var key in paramsObj) {
						src += '&' + key + '=' + paramsObj[key];
					}
				}
				
				//create iframe com channel
				iframe = document.createElement('iframe');
				iframe.src = 'iframe.html';//todo: make dynamic
				iframe.style.display = 'none';
				document.body.appendChild(iframe);
				
				//make req
				iframe.src = src;
				
				//if callback fn defined, use it
				if (fn) {
					completeCallback = fn;
				}
			};
			
			//init
			//fetch dev key case-hardened-js-style
			var scriptNodes = document.getElementsByTagName('script');
			for(var i = 0; i < scriptNodes.length; i++){

				//todo: make script src editable
				if(-1 !== scriptNodes[i].src.indexOf('service/library.js')){

					//todo: verify we're trimming all whitespace
					hash = scriptNodes[i].innerHTML.replace('\n', '');
					hash = scriptNodes[i].innerHTML.replace(' ', '');
				}
			}

			//validate existence of hash
			if (!hash) {
				throw('hash required in sdk lib script include block');
			}

			//create iframe com channel
			iframe = document.createElement('iframe');
			iframe.src = 'iframe.html';//todo: make dynamic
			iframe.style.display = 'none';
			document.body.appendChild(iframe);
			
			//1st req is for fetching crumb
			request(null, function (data) {
				console.log(data);
				
				//static crumb for testing
				crumb = '4dc49e30be212209c559e0a990d8f3f8';
				
				//if requests are made b4 crumb returns, requests are queued.  Now the crumb is ready, so exec queued requests
				request(requestQueue[0].params, requestQueue[0].callback);
			});
			
		return {
			//the main, general purpose util
			'request' : request,
			
			//iframe callback needs to be exposed so iframe can access it
			'iframeCallback' : iframeCallback
		};
	}());