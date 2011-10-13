<?php

$languageCode='EN';
initHittr();

switch (getTriageValue()) {
	case 'NEW_REQUEST':
		print generateUploadPage();
	break;

	case 'UPLOAD_REQUEST':
		$hashToUse=getUnusedHash();
		print generateUploadNotification($hashToUse,moveAndWriteFile($hashToUse,realpath('./files'),$_FILES['file']));
	break;

	case 'RETRIEVAL_REQUEST':
		$hashToSend=$_GET['h'];
		$fileToSend=glob("files/$hashToSend*");
		if (sizeof($fileToSend)>0) {
			$fileInfoString=substr(str_replace('files/','',$fileToSend[0]),32);
			$fileInfoArray=explode('|',Base32::Decode($fileInfoString));
			header('Content-type: '.trim($fileInfoArray[1]));
			readfile($fileToSend[0]);
			unlink($fileToSend[0]);
		}
		print generateUploadPage();
	break;
}


function initHittr() {
	include('./encode32.php');
	include('./language.php');
	checkPreRequisites();
}


function checkPreRequisites() {
	if (!is_writable('./files')) {
		dieWithError(getLanguageString('FILES_NOWRITE'));
	}
}


function getTriageValue() {
	if (isset($_FILES['file'])) {
		return 'UPLOAD_REQUEST';
	} elseif (isset($_GET['h'])) {
		return 'RETRIEVAL_REQUEST';
	}
	return 'NEW_REQUEST';
}


function generateUploadPage(){
	$title='oneHittr';
	return generateDisplayPage("<h1>$title</h1>\n".generateUploadForm());
}


function generateUploadForm(){
	$filenameString=getLanguageString('FILENAME');
	$submitString=getLanguageString('SUBMIT');

	$formHtml=<<<EOT
<form action="{$_SERVER['PHP_SELF']}" method="post" enctype="multipart/form-data">
		<p><label for="file">$filenameString:</label>
		<input type="file" name="file" id="file" />
			<br />
		<input type="submit" name="submit" value="$submitString" /></p>
	</form>
EOT;
	return $formHtml;
}


function getUnusedHash() {
	$testHash=FALSE;
	while (sizeof(glob("files/$testHash*")) > 0 || !$testHash) {
		$testHash=genrandHash();
	}
	return $testHash;
}


function genrandHash() {
	return md5(pack('N8', mt_rand(), mt_rand(), mt_rand(),mt_rand(), mt_rand(), mt_rand(),mt_rand(),mt_rand()));
}


function generateUploadNotification($hashToUse='') {
	$fileAvailableString=getLanguageString('FILE_AVAILABLE');
	$singleAccessWarningString=getLanguageString('SINGLE_ACCESS_WARNING');

	$notificationHTML=<<<EOT
	<p>$fileAvailableString</p>
		<ul><li><a href='http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}/?h=$hashToUse'>http://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}/?h=$hashToUse</a></li></ul>
	<p>$singleAccessWarningString</p>
EOT;
	return generateDisplayPage($notificationHTML);
}


function moveAndWriteFile($hashToUse,$targetDirectory,$fileArray) {
	$base32String=Base32::encode($fileArray['name'].'|'.$fileArray['type']);
	move_uploaded_file($fileArray['tmp_name'],$targetDirectory.'/'.$hashToUse.$base32String);
}


function dieWithError($errorString) {
	die('hittr : '.$errorString);
}


function generateDisplayPage($bodyToUse) {
	$bodyHTML=<<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<title>oneHittr</title>
	</head>
	<body>
		$bodyToUse
	</body>
</html>
EOT;
	return $bodyHTML;
}


?>
