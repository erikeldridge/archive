// credit: http://github.com/drgath/drgath.github.com/tree/master/talks/20100515_MusicHackday
function foobar() {
	var weatherZipInput = document.getElementById('weatherZipInput').value;
	executeYQL("SELECT * FROM weather.forecast WHERE location='" + weatherZipInput + "'", "foobarCB");
}

function foobarCB(response) {
	var timerStop = +new Date();console.log(response);
	var item = response.query.results.channel.item;
	var html = item.title + "\n" + item.condition.temp + " degrees and " + item.condition.text + "\n\n" + timerStart + " - " + timerStop + " = " + (timerStop - timerStart) + " milliseconds";

	document.getElementById("asd").innerHTML = html;
}

function executeYQL(yql, callbackFuncName) {
	timerStart = +new Date();
	var url = "http://query.yahooapis.com/v1/public/yql?q=" + encodeURIComponent(yql) + "&env=store://datatables.org/alltableswithkeys&format=json&callback="+callbackFuncName + "&cacheBuster=" + (+new Date());
	var head = document.getElementsByTagName('head')[0];
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url;
	head.appendChild(script);
}
