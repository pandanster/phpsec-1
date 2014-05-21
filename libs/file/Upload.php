<?php
namespace phpsec;
/**
 * Required Files
 */
require_once (__DIR__ . '/core/random.php');
require_once (__DIR__ . '/core/time.php');



class Upload
{
	
 	/**
	 * Maximum allowed file size for file upload.
	 * @var int
 	*/
	public static $maximumSize = 2097152;
	
	/**
	 * Allowed extensions 
	 * @var array
	**/
	public static $allowedExtensions= array("jpeg"=>"jpeg","gif"=>"gif","pdf"=>"pdf","jpg"=>"jpg",
											"png"=>"png","txt"=>"txt");

	/**
	 * Allowed mimetypes 
	 * @var array
	**/
	public static $allowedMIMETypes= array("text/plain"=>"text/plain",
											"application/vnd.openxmlformats-officedocument.wordprocessingml.document"
											=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
											"image/jpeg"=>"image/jpeg","image/gif"=>"image/gif",
											"application/x-tar"=>"application/x-tar",
											"application/pdf"=>"application/pdf",
											"image/x-ms-bmp"=>"image/bmp",
											"application/pdf"=>"application/pdf");


	/**
	 *Receives the file name and tests if its allowed and valid, only allows the filename to contain 
	 *@param string $string contains the file name to be tested
	 *@return boolean true if file name is valid
	 *
	**/
	public static function isValidName($field)
	{

		if(!($_FILES[$field]))
		{
			return FALSE;
		}
		else
			$file_name=$_FILES[$field]["name"];
	 $pattern="/^([A-Z]|[a-z]|[0-9]|[_-])+\.[a-z]{3,4}$/";
	 preg_match($pattern, $file_name, $matches);
	 if(count($matches) == 0)
	 	return FALSE;
	 else
	 	return TRUE;

	}

	/**
	 * Receives the file name and checks for the file size , if the size is within the allowed limit 
	 * returns true
	 * @param string $field filename identifier
	 * @return Boolean TRUE if within acceptable, fals if not within acceptable limit
	 **/

	 public static function isSizeOk($field)
	 {
	 	if(!($_FILES[$field]))
	 		return FALSE;
	 	else
	 	{ 		
	 		if ($_FILES[$field]["size"] > Upload::$maximumSize)
	 			return FALSE;
	 		else
	 			return TRUE;
	 	}
	 }

	 /**
	  * Receives the file name and checks if the provided extensions is part of allowed extensions
	  * @param string $field file identifier
	  * @return boolean true is allowed extension, false if not
	  **/
	 public static function isExtensionOk($field)
	 {

	 	if(!($_FILES[$field]))
		{
			return FALSE;
		}
		else
		{
		$file_name=$_FILES[$field]["name"];
	 	$pattern="/^([A-Z]|[a-z]|[0-9]|[_-])+\.([a-z]{3,4})$/";
		 preg_match($pattern, $file_name, $matches);
	 	if(count($matches) == 0)
		 	return FALSE;
		 else
	 		{
		
	 			if (!(array_key_exists($matches[2],Upload::$allowedExtensions)))
	 				return FALSE;
	 			else
	 				return TRUE;
			}
	 		}
	 }

	 /**
	  * Check if the provided file is just image
	  * @param string $field file identifer 
	  * @return boolean returns true if file is identified to be image else returns false
	  **/
	 public static function isImage($field)
	 {
	 	if(!($_FILES[$field]))
		{
			return FALSE;
		}
		else
		{
		$file_name=$_FILES[$field]["name"];
	 	$pattern="/^([A-Z]|[a-z]|[0-9]|[_-])+\.([a-z]{3,4})$/";
		preg_match($pattern, $file_name, $match);
		$temp_name=$_FILES[$field]["tmp_name"];
		$failed ='n';
		error_reporting(0);
		if(($match[2] === "jepg") or ($match[2] === "jpg"))
		{
			if(!($img=imagecreatefromjpeg($temp_name)))
				$failed = 'y';
		}
		if($match[2] === "png")
		{
			if(!($img=imagecreatefrompng($temp_name)))
				$failed = 'y';
		}
		if($match[2] === "gif")
		{
			if(!($img=imagecreatefromgif($temp_name)))
				$failed = 'y';
		}
		error_reporting(E_ALL);
		if ($failed === 'y')
			return FALSE;
		else
		{
			$imageinfo = getimagesize($temp_name);
			if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/png' )
				return FALSE;
			else
				return TRUE;

		}
		}
	}


	/**
	 *Checks if the file type is allowed based on MimeType 
	 *@param string $field the identifier for the file 
	 *@return boolean if the file type is not allowed return false
	 **/
	public static function isFileTypeReal($field)
	{
		$finfo=new finfo(FILEINFO_MIME_TYPE);
		$file_contents=file_get_contents($_FILES[$field]['tmp_name']);
		$mime_type=$finfo->buffer($file_contents);
		if (array_key_exists($mime_type,Upload::$allowedMIMETypes))
		{

			if(upload::$allowedMIMETypes[$mime_type] === $_FILES[$field]['type'])
				return TRUE;
			else
				return FALSE;

		}
	 	else
	 		return FALSE;
			}
}


?>