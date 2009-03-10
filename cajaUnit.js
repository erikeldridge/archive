
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
	cajaUnit = function(){
		var settings = {
			'outputId':'output',
			'passColor':'green',
			'failColor':'red'
		};
		return {
			'settings':settings,
			'createSuite':function(customSettings){
				var tests = [],
					suiteSettings = {},
					div = document.createElement('div');
				suiteSettings.suiteId = ('suite' + new Date().getTime()/1000).replace('.','');
				foreach(cajaUnit.settings, function(name, value){
					suiteSettings[name] = value;
				});
				if(customSettings){
					foreach(customSettings, function(name, value){
						suiteSettings[name] = value;
					});
				}
				return{
					'suiteSettings':suiteSettings,
					'addTest':function(fn){
						foreach(suiteSettings, function(name, value){
							fn.testSettings[name] = value;
						});
						fn.testSettings.outputId = suiteSettings.suiteId;
						tests.push(fn);
					},
					'run':function(){
						div.appendChild(
							document.createTextNode(suiteSettings.suiteName));
						div.id = suiteSettings.suiteId;
						document.getElementById(suiteSettings.outputId).appendChild(div);
						foreach(tests, function(index, test){
							test.run();
						});
					}
				};
			},
			'createTest':function(customSettings){
				var testSettings = {};
				foreach(cajaUnit.settings, function(name, value){
					testSettings[name] = value;
				});
				if(customSettings){
					foreach(customSettings, function(name, value){
						testSettings[name] = value;
					});
				}
				return{
					'testSettings':testSettings,
					'run':function(){
						var div = document.createElement('div'),
							setUpResults;
	    				if(testSettings.setUp){
							setUpResults = testSettings.setUp();
	    				}
	    				if(testSettings.test(setUpResults)){//pass
							div.style.color = testSettings.passColor;
	    				}else{
	    					div.style.color = testSettings.failColor;
	    				}
						div.appendChild(
							document.createTextNode(testSettings.testName));
	    				if(testSettings.tearDown){
	    					testSettings.tearDown(setUpResults);
	    				}
						document.getElementById(testSettings.outputId).appendChild(div);
					}
				};
			}
		};
	}();