<?php

function getLanguageString($messageIdentifier) {
	global $languageCode;
	$messages = array(
		'EN'=> array(
			'FILENAME'=>'Filename',
			'FILE_AVAILABLE'=>'Your file is now available at:',
			'SINGLE_ACCESS_WARNING'=>'It will be there ONLY for a single request, then it will dissapear. If you wish to access it multiple times, upload it again.',
			'SUBMIT'=>'Submit',
			'FILES_NOWRITE'=>'Files directory is not writable',
		),
	);
	if (isset($messages[$languageCode][$messageIdentifier])) {
		return $messages[$languageCode][$messageIdentifier];
	}
	return;
}

?>
