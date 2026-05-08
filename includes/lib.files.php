<?php

/** RZM PHP Framework **/

function file_upload($tmp_file_name, $path, $new_file_name) {

	# verify info
	if( !file_exists($tmp_file_name) || !is_file($tmp_file_name))
		return "Faltan datos del archivo!";
		
	# verify path
	if(!file_exists($path))
		mkdir($path);
	
	# verify & upload
	if (is_uploaded_file($tmp_file_name)) {
	
		if(move_uploaded_file($tmp_file_name, $path.$new_file_name)) {

			return true;
			
		} else {
			
			return "El archivo no se pudo guardar!";
		
		}

	} else {

		return "El archivo no se cargó correctamente!";

	}
	
}


function file_delete($full_file_path) {

	# verify file path & name
	if(var_is_empty($full_file_path) || !file_exists($full_file_path) || !is_file($full_file_path)) {
		return false;
	}
	
	# delete file
	return unlink($full_file_path);

}



function file_download($full_file_path, $app_type="zip")  {

	# verify file name
	if(var_is_empty($full_file_path)) {

		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function file_download: path is empty.");
			
		}

		return false;

	}

	# vars
	$full_path 	= base64_decode($full_file_path);
	$file_name	= basename($full_path);
	
	if(!file_exists($full_path)) { // File doesn't exist, output error
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function file_download: file $full_path doesn't exist.");
			
		} else {
		
			error_log("function file_download: file $full_path doesn't exist.");
			
		}
	
	} else {	// Set headers
	
		# set headers
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=\"$file_name\"");
		header("Content-Type: application/$app_type");
		header("Content-Transfer-Encoding: binary");
		
		# Read the file from disk
		readfile($full_path);
		
	}

}


function file_open($full_file_path)  {

	# verify file name
	if(var_is_empty($full_file_path)) {
		if(ENVIRONMENT=="DEVELOPMENT") { 
			die("function file_open: path is empty.");
		}
		return false;
	}

	# vars
	$full_path 	= base64_decode($full_file_path);
	$file_name	= basename($full_path);
	$file_type = pathinfo($file_name, PATHINFO_EXTENSION);
	
	if(!file_exists($full_path)) { // File doesn't exist, output error
	
		if(ENVIRONMENT=="DEVELOPMENT") { 
		
			die("function file_download: file $full_path doesn't exist.");
			
		} else {
		
			error_log("function file_download: file $full_path doesn't exist.");
			
		}
	
	} else {	// Set headers
	
		# set headers
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$file_name");
		header("Content-Type: application/$file_type");
		header("Content-Transfer-Encoding: binary");
		
		# Read the file from disk
		readfile($full_path);
		
	}

}


function file_is_valid($full_file_path) {

	return (file_exists($full_file_path) && is_file($full_file_path));

}


function file_filter_filename($filename, $beautify=true) {

	$filename = preg_replace(
		'~
		[<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
		[\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
		[\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
		[#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
		[{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
		~x',
		'-', $filename);

	// avoids ".", ".." or ".hiddenFiles"
	$filename = ltrim($filename, '.-');

	// optional beautification
	if ($beautify) {
		$filename = file_beautify_filename($filename);
	}

	// maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
	$ext = pathinfo($filename, PATHINFO_EXTENSION);
	$filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');

	return $filename;

}


function file_beautify_filename($filename) {
	
	// reduce consecutive characters
    $filename = preg_replace( array('/ +/', '/_+/', '/-+/'), '-', $filename );
	$filename = preg_replace( array('/-*\.-*/', '/\.{2,}/'), '.', $filename );
	
    // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
    //$filename = mb_strtolower($filename, mb_detect_encoding($filename));
	
	// ".file-name.-" becomes "file-name"
	$filename = trim($filename, '.-');
	
	return $filename;
	
}


?>