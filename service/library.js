var sdk = (function () {
	
		var totalQtyChunks = qtyChunksCollected = chunkStr = iframe = null,
			completeCallback = console.log,
		
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

		               //do something when data's completely loaded
		               completeCallback(data);

		               //clear affiliate vars for next use
		               totalQtyChunks = qtyChunksCollected = chunkStr = null;
		            }
		        }
		    };

			//initialize by creating the iframe comm channel and requesting crumb
			iframe = document.createElement('iframe');
			iframe.src = 'iframe.html';//todo: make dynamic
			iframe.style.display = 'none';
			document.body.appendChild(iframe);
			this.request();
			
		return {
			
			//the general purpose util
			'request' : function (params, fn) {
				
				//todo: allow global def of service url
				var src = 'service';
				
				if (params) {
					//todo: format params
				}
				iframe.src = src;
				if (fn) {
					completeCallback = fn;
				}
			},
			
			//iframe callback needs to be exposed so iframe can access it
			'iframeCallback' : iframeCallback
		};
	}());