
var foreach = function(collection, callback){//a convenient loop pattern
		if(collection && collection.length){//array or node list
			for(var i = 0; i < collection.length; i++){
				callback(i, collection[i]);
			}
		}else if(collection && collection.hasOwnProperty){//object
			for(var key in collection){
				if(collection.hasOwnProperty(key)){
					callback(key, collection[key]);
				}
			}
		}else{
			throw('foreach() error: collection (' + collection + ') is neither an array nor an object');
		}
	},
	cajaUnit = function(){
		//define global settings
		var settings = {
			'outputId':'output',//the dom root to append all output to
			'passColor':'green',//the css color of text for passing test output 
			'failColor':'red'//the css color of text for failing test output
		};
		return {
			//create a handle for global settings so we can set them if necessary
			'settings':settings,
			//define a creation method to generate an executable test suite object
			'createSuite':function(customSettings){
				var tests = [],
					suiteSettings = {},
					div = document.createElement('div');//the suite's output wrapper
				//create quasi-unique id for the suite so we can find it through the dom latr
				suiteSettings.suiteId = ('suite' + Math.random()).replace('.','');
				//load global settings into local suite settings
				foreach(cajaUnit.settings, function(name, value){
					suiteSettings[name] = value;
				});
				//load/override local settings w/ custom ones
				if(customSettings){//foreach throws error on undefined input, so check first
					foreach(customSettings, function(name, value){
						suiteSettings[name] = value;
					});
				}
				return{
					'suiteSettings':suiteSettings,//used only for debugging
					//define function to add test to suite
					'addTest':function(fn){
						//load/override function settings w/ suite settings so we can set stuff in the suite and have it apply to all functions
						foreach(suiteSettings, function(name, value){
							fn.testSettings[name] = value;
						});
						//we want a suite's test output to append to the suite wrapper, not the global wrapper
						fn.testSettings.outputId = suiteSettings.suiteId;
						//add the test
						tests.push(fn);
					},
					//define fn to execute all test in the suite
					'run':function(){
						//print the suite name at the top of the suite's output
						div.appendChild(
							document.createTextNode(suiteSettings.suiteName));
						//assign the suite the quasi-unique id
						div.id = suiteSettings.suiteId;
						//append the suite root to the page now so the tests run later can find it
						document.getElementById(suiteSettings.outputId).appendChild(div);
						//execute the tests
						foreach(tests, function(index, test){
							test.run();
						});
					}
				};
			},
			//define a creation method to generate an executable test object
			'createTest':function(customSettings){
				var testSettings = {};
				//load default settings from cajaUnit, so we can optionally run a test outside of a suite
				foreach(cajaUnit.settings, function(name, value){
					testSettings[name] = value;
				});
				//if there are custom settings, load them
				if(customSettings){
					foreach(customSettings, function(name, value){
						testSettings[name] = value;
					});
				}
				return{
					//create handle to local settings so a suite can override them if necessary
					'testSettings':testSettings,
					//define a function to execute a test
					'run':function(){
						var div = document.createElement('div'),//the test's output wrapper
							setUpResults;
						//if a set up function was defined, run it and capture the output
	    				if(testSettings.setUp){
							setUpResults = testSettings.setUp();
	    				}
						//this is the core of everything: the test to run.  Pass in the results (may be undefined) of the set up function
	    				try{
							testSettings.test(setUpResults);
							//test passes
							div.style.color = testSettings.passColor;//you could also define a className here
	    				}catch(e){//test fails
	    					div.style.color = testSettings.failColor;console.log(e);
	    				}
						//print the test's name
						div.appendChild(
							document.createTextNode(testSettings.testName));
						//if a tear down function was defined, run it.  Pass in the set up results in case we want to edit/destroy them
	    				if(testSettings.tearDown){
	    					testSettings.tearDown(setUpResults);
	    				}
						//append the output to the parent.  If we're running in a suite, this will be defined by the suite, else by cajaUnit
						document.getElementById(testSettings.outputId).appendChild(div);
					}
				};
			},
			'assertTrue':function(val){
				if(true !== val){
					throw('');
				}
			},
			'assertFalse':function(val){
				if(false !== val){
					throw('');
				}
			},
			'assertEqual':function(val1, val2){
				if(val1 !== val2){
					throw('');
				}
			}
		};
	}();