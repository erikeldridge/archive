/**
 * @package    http://github.com/erikeldridge/foxbat/tree/master
 * @copyright  (c) 2009, Erik Eldridge, all rights reserved
 * @license    BSD Open Source License
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/
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