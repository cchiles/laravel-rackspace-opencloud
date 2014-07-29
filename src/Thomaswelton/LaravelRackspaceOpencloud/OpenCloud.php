<?php namespace Thomaswelton\LaravelRackspaceOpencloud;

use \Config;
use \File;
use Alchemy\Zippy\Zippy;
use OpenCloud\Rackspace;
use OpenCloud\ObjectStore\Resource\DataObject;


// 5 minutes
define('RAXSDK_TIMEOUT', 300);

class OpenCloud extends Rackspace
{

	public $region = null;

	function __construct(){

		$this->region = Config::get('laravel-rackspace-opencloud::region');
		$authUrl = ($this->region == 'LON') ? 'https://lon.identity.api.rackspacecloud.com/v2.0/' : 'https://identity.api.rackspacecloud.com/v2.0/';

		parent::__construct($authUrl, array(
			'username' => Config::get('laravel-rackspace-opencloud::username'),
			'apiKey' => Config::get('laravel-rackspace-opencloud::apiKey')
		));
	}

	public function getObjectStore()
	{
		return $this->objectStoreService('cloudFiles', $this->region);
	}

	public function getContainer($name)
	{
		// create a new container
		$container = $this->getObjectStore()->getContainer($name);

		return $container;
	}

	public function uploadObject($container, $file, $name = null)
	{
		$container = $this->getContainer($container);

		$headers = array(
			"Access-Control-Allow-Origin" => "*"
		);

		return $container->uploadObject($name, $file, $headers);
	}

	public function getObject($container, $name = null)
	{
		$container = $this->getContainer($container);

		return $container->getObject($name);
	}

	public function getObjectList($container)
	{
		$container = $this->getContainer($container);

		return $container->objectList();
	}

	public function getPartialObject($container, $name = null)
	{
		$container = $this->getContainer($container);

		return $container->getPartialObject($name);
	}

 //    // Create and archive and upload a whole directory
 //    // $dir - Directory to upload
 //    // $cdnDir - Directory on the CDN to upload to
 //    // $dirTrim - Path segments to trim from the dir path when on the CDN
 //    public function uploadDir($container, $dir, $cdnDir = '', $dirTrim = ''){
 //        $temp_file =  storage_path() . '/CDN-' . time() . '.tar.gz';

 //        $zip_dir_name = (0 === strpos($dir, $dirTrim)) ? substr($dir, strlen($dirTrim) + 1) : $dir;

 //        $zippy = Zippy::load();
 //        // creates an archive.zip that contains a directory "folder" that contains
 //        // files contained in "/path/to/directory" recursively
 //        $archive = $zippy->create($temp_file, array(
 //            $cdnDir . '/' . $zip_dir_name => $dir
 //        ), true);

 //        $cdnFile = $this->createDataObject($container, $temp_file, '/', 'tar.gz');

 //        File::delete($temp_file);

 //        return $cdnFile;
 //    }

 //    public function exists($container, $file){
 //        $container = $this->getContainer($container);
 //        try{
 //            return $container->DataObject($file);
 //        }catch(\OpenCloud\Common\Exceptions\ObjFetchError $e){
 //            return false;
 //        }
 //    }

	// public function createDataObject($container, $filePath, $fileName = null, $extract = null)
	// {
	// 	if(is_null($fileName)){
	// 		$fileName = basename($filePath);
	// 	}

	// 	$container = $this->getContainer($container);

	// 	$headers = array(
	// 		"Access-Control-Allow-Origin" => "*"
	// 	);

	// 	$object = new DataObject();
	// 	$object->Create(array('name'=> $fileName, 'extra_headers' => $headers), $filePath, $extract);

	// 	return $object;
	// }

  // public function delete($container, $file){
  //     $container = $this->getContainer($container);
  //     //if file is fed with full url, shorten to last component
  //       $file = explode('/',$file);
  //       $file = end($file);
  //     try{
  //         return $container->DataObject($file)->delete();
  //     }catch(\OpenCloud\Common\Exceptions\ObjFetchError $e){
  //         return $e;
  //     }
  // }
}

