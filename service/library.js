var sdk = (function () {
	
		var totalQtyChunks = qtyChunksCollected = chunkStr = iframe = null,
			completeCallback = function (data, fn) {
		        fn(data);
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

		               //do something
		               completeCallback(data, console.log);

		               //clear affiliate vars for next use
		               totalQtyChunks = qtyChunksCollected = chunkStr = null;
		            }
		        }
		    };
		    //iframe = document.createElement('iframe');
		return {
			'request' : function (params, fn) {
				var src = 'service';
				if (params) {
					//todo: format params
				}
				iframe.src = src;
				if (fn) {
					completeCallback = fn;
				}
			},
			'query' : function (yql) {
				
			},
			'iframeCallback' : iframeCallback,
			'init' : function () {
				iframe = document.createElement('iframe');
				iframe.src = 'iframe.html';//todo: make dynamic
				iframe.style.display = 'none';
				document.body.appendChild(iframe);
				this.request();
			}
		};
	}());
	
//todo: set hash in here
sdk.init();