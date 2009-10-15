var totalQtyChunks = qtyChunksCollected = chunkStr = null,
    completeCallback = function (data) {
        console.log(data);
    },
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
               completeCallback(data);

               //clear affiliate vars for next use
               totalQtyChunks = qtyChunksCollected = chunkStr = null;
            }
        }
    },
    iframe = document.createElement('iframe');

//init iframe so browser caches file
iframe.src = 'iframe.html';
iframe.style.display = 'none';
document.body.appendChild(iframe);

//make service call
iframe.src = 'service/?method=profile&hash=asd123';