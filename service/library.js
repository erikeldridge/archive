//todo: look into using a standard js lib implementation of iframe comm util
var sdk = (function () {
	
		var totalQtyChunks = null,
			qtyChunksCollected = null,
			chunkStr = null,
			iframe = null,
			hash = null,
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
			request = function (paramsObj, fn) {
				
				//todo: allow global def of service url
				var src = 'service/';
				
				if (paramsObj) {
					src += '?';
					
					//append params to req url
					//todo: either use foreach or add hasOwnObject check js-good-stuff-style
					for (var key in paramsObj) {
						src += key + '=' + paramsObj[key] + '&';
					}
					
					//trim trailing &
					src = src.substr(0, src.length - 1);
				}
				
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
				}
			}
			
			//create iframe com channel
			iframe = document.createElement('iframe');
			iframe.src = 'iframe.html';//todo: make dynamic
			iframe.style.display = 'none';
			document.body.appendChild(iframe);
			
			//fetch only crumb in 1st request
			request(null, function(data){
				//todo: cache crumb internally
				console.log(data);
			});
			
		return {
			
			//the main, general purpose util
			'request' : request,
			
			//iframe callback needs to be exposed so iframe can access it
			'iframeCallback' : iframeCallback
		};
	}());