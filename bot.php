<?
//BEGIN: params
//irc
$host = "irc.freenode.net";
$port = 6667;
$nick = "pircbot";//the nickname that shows up in the chatroom
$desc = "this is a description";//not sure where this shows up, but it seems to be req'd
$chan = '#asdf';//"#asdf";//the chatroom name
//log
$file = 'log.txt';//the file to write IRC traffic to.  Note: new traffic will be appended to whatever's in the file
$end = strtotime('00:00 2/9/2009');//the date you want logging to end
//END: params
//BEGIN: logging
// open a socket connection to the IRC server
$fp = fsockopen($host, $port, $erno, $errstr, 30);
if (!$fp) {// print the error if there is no connection
    echo $errstr." (".$errno.")<br />\n";
} else {
    // write data through the socket to join the channel
    fwrite($fp, "USER $nick $host $nick: $desc\r\n");
	fwrite($fp, "NICK $nick\r\n");
    fwrite($fp, "JOIN $chan\r\n");
    // fwrite($fp, "PRIVMSG ".$chan." :logger online!\r\n");//uncomment if you want a confirmation msg on startup
    // loop through each line of input
	while (!feof($fp) && (time() < $end)) {//terminate loop if stream cuts or we reach end date
		$line = fgets($fp, 128);
		$ping = explode("PING :", $line);//the IRC server will occasionally "ping" the bot to maintain the connection
		echo 'input: '.$line;
		if($ping[1] && (false !== strpos($ping[1], 'freenode.net'))){// if we're pinged, "pong" back
			fwrite($fp, sprintf("PONG :%s\n", $ping[1])); //the host of the regional IRC server will be the 2nd arg in the ping
			echo 'output: '.sprintf("PONG :%s\n", $ping[1]);
		}elseif(false !== strpos($line, "PRIVMSG $chan :")){//if it's a message to the chatroom, log it
			$line = sprintf("[%s] %s", date(DATE_RFC822), $line);//prepending the date to line
			file_put_contents($file, $line, FILE_APPEND);// write everything to file. there may be useful info other than the name and msg. we can format for presentation later.
		}
	}
	fclose($fp);
}
?>
