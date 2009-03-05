var cajaUnit = function(){
    var foreach = function(collection, callback){
		if(collection && collection.length){//array or node list
    		for(var i = 0; i < collection.length; i++){
    			callback(i, collection[i]);
    		}
    	}else if(collection && collection.hasOwnProperty){
    		for(var key in collection){
    			if(collection.hasOwnProperty(key)){
    				callback(key, collection[key]);
    			}
    		}
    	}else{
    		throw('foreach() error: collection (' + collection + ') is neither an array nor an object');
    	}
    },
	sprintf = function (/*format, arg1, arg2  ...*/) {
		var params = Array.prototype.slice.apply(arguments),//cache arguments quasi-array as actual array.  ref: http://www.hunlock.com/blogs/Functional_Javascript#quickIDX11
			format = params[0],
			args = params.slice(1),
			pieces = format.split('%%');
		foreach(args, function (index, arg) {
			if (!arg) {
				throw('sprintf() error: expecting string, but received'+arg+', which is a'+typeof arg+'Context: '+params.toString());
			} else {
				pieces[index] = pieces[index] + arg.toString();
			}
		});
		return pieces.join('');
	};
	return {
    	'createSuite':function(customSettings){
    	    //define default settings
			var settings = {};
    		settings.suiteName = 'New Suite';
    		settings.outputId = 'output';
			//override defaults w/ custom, if defined
			if(customSettings){
				foreach(customSettings, function(name, value){
					settings[name] = value;
				});
			}
    		var tests = [];
            return {
				'settings':settings,// not req'd, but included for debugging
                'addTest':function(fn){
					//override test's defaults
    				foreach(settings, function(name, value){
    					fn.settings[name] = value;
    				});
					//add test to run queue
    				tests.push(fn);
    			},
    			'run':function(){
    			    //print header
					var html = sprintf('<div class="suite">%%</div>', settings.suiteName);
					document.getElementById(settings.outputId).innerHTML += html;
					//run tests
    				foreach(tests, function(i, test){
    					test.run();
    				});
    			}
            };
        },
        'createTest':function(customSettings){
    	    //default settings
			var settings = settings || {};
    		settings.passClassName = 'pass';
    		settings.failClassName = 'fail';
    		settings.testName = 'New Test';
			//override defaults w/ custom, if defined
			if(customSettings){
				foreach(customSettings, function(name, value){
					settings[name] = value;
				});
			}
    		return {
    			'settings':settings,//made public so it can be set by suite w/o set method
    			'run':function(){
    				if(settings.setUp){
    					var setUpResults = settings.setUp();
    				}
    				if(settings.test(setUpResults)){
    					var html = sprintf('<div class="%%">%%</div>', settings.passClassName, settings.testName);
    					document.getElementById(settings.outputId).innerHTML += html;
    				}else{
    					var html = sprintf('<div class="%%">%%</div>', settings.failClassName, settings.testName);
    					document.getElementById(settings.outputId).innerHTML += html;
    				}
    				if(settings.tearDown){
    					settings.tearDown(setUpResults);
    				}
    			}
    		};
    	}
    };
}();