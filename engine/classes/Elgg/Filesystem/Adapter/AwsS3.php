<?php
namespace Elgg\Filesystem\Adapter;
use Gaufrette\Filesystem;
use Gaufrette\File;
use Gaufrette\Adapter\AwsS3 as GaufretteAwsS3;
use Aws\S3\S3Client;

class AwsS3 extends Filesystem implements Adapter {
	private $config;
	public function __construct(array $config) {
		$this->config = $config;
		
		foreach (['bucket', 'key', 'secret'] as $req) {
			if (!isset($config[$req])) {
				throw new \InvalidArgumentException("Config passed to Filesystem\Adapter\AwsS3 must have $req");
			}
		}
		
		$s3 = S3Client::factory($config);
		$adapter = new GaufretteAwsS3($s3, $config['bucket']);
		parent::__construct($adapter);
	}
	
	/**
	 * 
	 * @param type $name
	 * @return File
	 */
	public function file($name) {
		return new File($name, $this);
	}
	
	/**
	 * 
	 * @param type $path
	 * @return \self
	 */
	public function directory($path) {
		// path is normalized by gaufrette
		$config = $this->config;
		$config['path'] = "{$config['path']}/$path";
		return new self($config);
	}
	
	public static function getPathPrefix($guid) {
		return "/$guid/";
	}
}