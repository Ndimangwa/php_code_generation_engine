<?php 
class SystemFiles {
    public static function getUploadedFileExtension($fileArray1)	{
		$temp = explode(".", $fileArray1['name']);
        if (sizeof($temp) == 1) return "";
		$fileextension = end($temp);
		$fileextension = strtolower($fileextension);
		return $fileextension;
	}
	public static function isThereAnyFileReceivedFromTheClient($fileArray1)	{
		return ! ($fileArray1['name'] == "" || $fileArray1['type'] == "" || $fileArray1['tmp_name'] == "");
	}
	public static function checkUploadedFile($fileArray1, $validTypes, $validExtensions, $maximumUploadedSize)	{
		$promise1 = new Promise();
		if (intval($fileArray1['error']) != 0)	{
			$promise1->setReason("Error Occured during uploading of the file");
			return $promise1;
		}
		/*if (! (is_null($validTypes) || in_array(strtolower($fileArray1['type']), $validTypes)))	{
			$promise1->setReason("Types not in a range of valid Types");
			return $promise1;
		}*/
		$fileextension = self::getUploadedFileExtension($fileArray1);
		if (! (is_null($validExtensions) || in_array(strtolower($fileextension), $validExtensions)))	{
			$promise1->setReason("File Extension not in a range of valid Extensions");
			return $promise1;
		}
		if (intval($fileArray1['size']) > intval($maximumUploadedSize))	{
			$promise1->setReason("The File has exceeded the file Limit");
			return $promise1;
		}
		$promise1->setResults($fileArray1['tmp_name']); //You can use this read and do your things
		$promise1->setPromise(true);
		return $promise1;
	}
	public static function saveUploadedFile($fileArray1, $folderToSave, $filename, $validTypes = null, $validExtensions = null, $maximumUploadedSize = 0)	{
		$promise1 = new Promise();
		if (intval($fileArray1['error']) != 0)	{
			$promise1->setReason("Error Occured during uploading of the file");
			return $promise1;
		}
		if (! (is_null($validTypes) || in_array(strtolower($fileArray1['type']), $validTypes)))	{
			$promise1->setReason("Types not in a range of valid Types");
			return $promise1;
		}
		$fileextension = self::getUploadedFileExtension($fileArray1);
		if (! (is_null($validExtensions) || in_array(strtolower($fileextension), $validExtensions)))	{
			$promise1->setReason("File Extension not in a range of valid Extensions");
			return $promise1;
		}
		if (($maximumUploadedSize != 0) && (intval($fileArray1['size']) > $maximumUploadedSize))	{
			$promise1->setReason("The File has exceeded the file Limit");
			return $promise1;
		}
        $absoluteFilePathToSave = join(DIRECTORY_SEPARATOR, [$folderToSave, $filename]);
		$absoluteFilePathToSave = str_replace( DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $absoluteFilePathToSave);
       /* if ($fileextension != "")   {
            $absoluteFilePathToSave .= ".".$fileextension;
        }*/
		if (! move_uploaded_file($fileArray1['tmp_name'], $absoluteFilePathToSave))	{
			$promise1->setReason("[ $absoluteFilePathToSave ] => Perhaps the server does not allow you to save this file");
			return $promise1;
		}
		$promise1->setResults($absoluteFilePathToSave);
		$promise1->setPromise(true);
		return $promise1;
	}
}
?>