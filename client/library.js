
/**
 * @package    foxbatexample
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
	var hash = null,
		requests = {},
		request = function (params, userCallback) {
			var iframe = document.createElement('iframe'),
				id = null,
				url = 'http://test.erikeldridge.com/foxbatexample',
				chunks = [],
				collected = 0,
				iframeCallback = function (obj) {
					var index = obj['index'];
					chunks[index] = obj['chunk'];
					collected++;
					
					//if collected equals total (coercion intended), we're done
					if (collected == obj['total']) {

						//do something w/ data
						userCallback(chunks.join(''));

						//clean up
						requests[id] = null;
						total = null;
						collected = null;
						chunks = null;
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
			
			//get hash from ui
			if (!hash) {
				var scriptNodes = document.getElementsByTagName('script');
				for (var i = 0; i < scriptNodes.length; i++) {
					if (-1 !== scriptNodes[i].src.indexOf('library.js')) {
						hash = scriptNodes[i].innerHTML;
					}
				}
				if (!hash) {
					throw('library js, request() fn, hash must be defined');
				}
			}
			
			//build out request params
			url += '/server/?id=' + id + '&hash=' + hash;
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