<?php
/* Copyright (c) 2013-2013 Simon Sickle <simon@simonsickle.com>
 *
 * This software distributed under the MIT License
 * http://www.opensource.org/licenses/mit-license.php
 *
 * This script is designed to protect files, to enable
 * a better adsense turnout. This script saves data to 
 * mysql, in a secure, unique, hash string that will likely
 * neve be duplicateable
 *
 */

require_once __DIR__ . '/resources/admin/dbconnect.php';

// Include the DirectoryLister class
require_once('resources/DirectoryLister.php');

// Initialize the DirectoryLister object
$lister = new DirectoryLister();


	// Get the filename given by directory linker
	$fileget = $_GET["file"];

	// Prevent downloading outside of directory listing bounds
	if (substr($fileget, 0, 1) == '/') {
		echo("Are you trying to do something very, verry naughty?");
	} elseif (substr($fileget, 0, 3) == '../') {
		echo("Are you trying to do something very, verry naughty?");
	} else {
		$file = $fileget;
	}

        $filepath = $lister->getFileBaseDir().$file;

	if(empty($_SERVER['REQUEST_URI'])) {
    	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
	}

	// Strip off query string so dirname() doesn't get confused
	$url = preg_replace('/\?.*$/', '', $_SERVER['REQUEST_URI']);
	$folderpath = 'http://'.$_SERVER['HTTP_HOST'].'/'.ltrim(dirname($url), '/');

	// Add some salt
        $s1 = md5('GetFilesToday134364529193sad5He%#ll##@@!oSa#ltmy12!@$@');
        $s2 = rand();
        $s3 = $s1.md5($s2.rand().$s1).$s1;
	// Generate the unique download key
	$key = $s1.$s3.uniqid(md5(rand())).$s2;

	// Get the activation time
	$time = date('U');

        // Write the key and activation time to the database as a new row.
        $registerid = mysql_query("INSERT INTO downloadkey (uniqueid,timestamp,filename) 
                                   VALUES(\"$key\",\"$time\",\"$filepath\")") or die(mysql_error());

// Create the filename
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

?>

<DOCUTYPE html>
<head>
<title><?php echo "Getting " . basename($filepath); ?></title>
<script type="text/javascript">
                    window.setTimeout(function() {
                        location.href = 'index.php';
                    }, 10000);
</script>
</head>
<body>
<p>
<center>
<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- files.simonsickle.com -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-1176090905311710"
     data-ad-slot="4540635854"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
<br>
<?php
if ($file != ""){
	$filename = basename($file);
	echo "<a href=\"$data\">$filename</a><br><br>";
} else {
	echo "<p>Error: File NOT found....</p><br><br>";
}


//Get MD5. Create and save if not in database already
if (file_exists($filepath)) {
    $query = sprintf("SELECT * FROM md5sums WHERE filename= '%s'",
        mysql_real_escape_string($filepath));
    $result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($result);
        if ($row) {
            echo "MD5: " . $row['md5'];
        } else {
            $md5 = md5_file($filepath);
            $sqlread = mysql_query("INSERT INTO md5sums (filename,md5) VALUES(\"$file\",\"$md5\")") or die(mysql_error());
            echo "MD5: " . $md5;
        }
} else {
    echo "<br>File doesn't exist";
}

echo "<br><br>";

echo "Redirecting in 10 seconds"; ?> </p>

<p>Click here if you are not redirected automatically in 10 seconds<br/>
            <a href="index.php">Get More Files</a>.
</p>
<?php
// Redirect to the download
if ($file != "") {
	echo '<META HTTP-EQUIV="Refresh" Content="2; URL=download.php?id=' . $key . '">';
} else {
	echo "Download isn't going to happen! What a shame.";
}
//show HTML below for 5 seconds
?>
<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- files.simonsickle.com -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-1176090905311710"
     data-ad-slot="4540635854"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</center>
</body>
</html>
