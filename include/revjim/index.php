<html><body><pre>
<?php

// Check and see if a feed has been supplied to us.
if(isset($_GET['feedurl'])) {
	$feedfile = $_GET['feedurl'];
} else {
	$feedfile = "test.xml";
}
//http://lanai.sourceforge.net/demo/include/revjim/index.php?feedurl=http://www.rssthai.com/rss/lastest.xml
// Sanitize the URL provided to us since
// some people can be very mean.
$feedfile = preg_replace("/\.\./","donthackthis",$feedfile);
$feedfile = preg_replace("/^\//","ummmmno",$feedfile);

// Require the xmlParser class
require_once('xmlParser.php');

// Require the feedParser class
require_once('feedParser.php');

// Create a need feedParser object
$p = new feedParser();

// Read in our sample feed file
$data = @implode("",@file($feedfile));

// Tell feedParser to parse the data
$info = $p->parseFeed($data);

// Print the data to the browser;
print "Getting: $feedfile\n";
print_r($info);

	
?>
</pre>
<?php

echo $info['channel']['title']."<br>";
echo $info['channel']['description']."<br>";
echo "<br><img src=".$info['image'][0]['url']." width=".$info['image'][0]['width']." height=".$info['image'][0]['height']." alt=\"".$info['image'][0]['description']."\" ></br>";
foreach ($info['item'] as $item ) {
		echo $item['title']."<br>";
		echo $item['description']."<br>";
		echo $item['link']."<br>";
		echo $item['date']."<br>";
	}	

?>
</body></html>

