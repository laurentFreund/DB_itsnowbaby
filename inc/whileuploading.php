<?php
// PUBLIC SETTINGS
$tmpdir = '../../../../../../tmp';			// your PHP temporary upload folder ( without last slash )
						// for this method is better set this folder in a dedicated one
						// to be sure that in that folder there isn't any other php temporary file

// APPLICATION - PLEASE DON'T MODIFY
require('UploadProgressManager.class.php');			// The class UploadProgressManager class
clearstatcache();						// and maybe a cleared stats
$UPM = new UploadProgressManager($tmpdir);			// new UploadProgressManager with temporary upload folder
if(($output = $UPM->getTemporaryFileSize()) === false)		// if UPM class cannot find the temporary file
	$output = -1;			// the output for LoadVars will be undefined
header('Content-Length: '.strlen($output));			// now headers to resolve browser cache problems
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
echo $output;							// and finally the output for LoadVars
?>