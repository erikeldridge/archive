<style>
.pass{
	color:green;
}
.fail{
	color:red;
}
</style>
<div id="output"><br/></div>
<script>//js included via php because yap requires js to be inline
	//<?= file_get_contents('cajaUnit.js') ?>//using js comment to avoid php throwing errors in js lint
</script>

<script>
//test the test unit
//BEGIN: createSuite
//test output
// var suite = cajaUnit.createSuite();
// suite.run();
// if(document.getElementById('output').innerHTML.match('<br/>[\t\n]*<div class="suite">New Suite</div>')){
//     document.getElementById('output').innerHTML += '<div class="pass">createSuite() :  test output</div>';
// }else{
//     document.getElementById('output').innerHTML += '<div class="fail">createSuite() :  test output</div>';
// }
// make sure cajaUnit settings stick through normal operation
cajaUnit.settings.failClassName = 'failed';
var suite = cajaUnit.createSuite(),
	test1 = cajaUnit.createTest();
suite.addTest(test1);
if('failed' === cajaUnit.settings.failClassName){
    document.getElementById('output').innerHTML += '<div class="pass">make sure cajaUnit settings stick through normal operation</div>';
}else{
    document.getElementById('output').innerHTML += '<div class="fail">make sure cajaUnit settings stick through normal operation</div>';
}

// test default settings
var suite = cajaUnit.createSuite();
if('output' === suite.suiteSettings.outputId){
    document.getElementById('output').innerHTML += '<div class="pass">createSuite() :  test default settings</div>';
}else{
    document.getElementById('output').innerHTML += '<div class="fail">createSuite() :  test default settings</div>';
}

//test name prints correctly
var suite = cajaUnit.createSuite({'suiteName':'test suite'});
suite.run();
var div = document.getElementById(suite.suiteSettings.suiteId);
if('test suite' === div.innerHTML){
    document.getElementById('output').innerHTML += '<div class="pass">test name prints correctly</div>';
}else{
    document.getElementById('output').innerHTML += '<div class="fail">test name prints correctly</div>';
}
//END: createSuite

//BEGIN: createTest
//default settings
var suite = cajaUnit.createSuite(),
    test1 = cajaUnit.createTest();
suite.addTest(test1);
if('object' === typeof test1.testSettings){
    document.getElementById('output').innerHTML += '<div class="pass">createTest() :  test default settings</div>';
}else{
    document.getElementById('output').innerHTML += '<div class="fail">createTest() :  test default settings</div>';
}

//custom settings
var suite = cajaUnit.createSuite(),
    test1 = cajaUnit.createTest({'testName':'test 1'});
suite.addTest(test1);
if('test 1' === test1.testSettings.testName){
    document.getElementById('output').innerHTML += '<div class="pass">createTest() :  test custom settings</div>';
}else{
    document.getElementById('output').innerHTML += '<div class="fail">createTest() :  test custom settings</div>';
}

//setUpResults
var suite = cajaUnit.createSuite({'suiteName':'test suite'}),
    test1 = cajaUnit.createTest({
	'testName':'test 1',
	'setUp':function(){
		var div = document.createElement('div');
		div.id = 'test';
		return {
	        'div':div
		};
	},
	'test':function(setUpResults){
		if('test' === setUpResults.div.id){
		    document.getElementById('output').innerHTML += '<div class="pass">createTest() :  setUpResults</div>';
		}else{
		    document.getElementById('output').innerHTML += '<div class="fail">createTest() :  setUpResults</div>';
		}
		return true;
	}
});
suite.addTest(test1);
suite.run();

//tearDown runs after test
var suite = cajaUnit.createSuite({'suiteName':'test suite'}),
    test1 = cajaUnit.createTest({
	'testName':'test 1',
	'setUp':function(){
		var div = document.createElement('div');
		div.id = 'test';
		document.getElementById('output').appendChild(div);
		return {};
	},
	'test':function(setUpResults){
		if(document.getElementById('test')){
		    document.getElementById('output').innerHTML += '<div class="pass">- sub-test - tearDown runs after test</div>';
		}else{
		    document.getElementById('output').innerHTML += '<div class="fail">- sub-test - tearDown runs after test</div>';
		}
		return true;
	},
	'tearDown':function(setUpResults){
		document.getElementById('output').removeChild(document.getElementById('test'));
	}
});
suite.addTest(test1);
suite.run();
if(!document.getElementById('test')){
    document.getElementById('output').innerHTML += '<div class="pass">tearDown runs after test</div>';
}else{
    document.getElementById('output').innerHTML += '<div class="fail">tearDown runs after test</div>';
}

//stand alone test
var test1 = cajaUnit.createTest({
	'outputId':'output',
	'testName':'this is my sample test',
	'test':function(){
		var foo = 1,
			bar = 1;
		return (foo === bar);
	}
});
test1.run();
</script>