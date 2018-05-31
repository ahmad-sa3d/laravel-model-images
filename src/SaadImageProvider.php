<?php

/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages;

use Saad\ModelImages\Contracts\ImageProviderContract;
use Saad\Image\Image;

class SaadImageProvider implements ImageProviderContract {

	/**
	 * Image Instance
	 * 
	 * @var Image
	 */
	protected $instance;


	/**
	 * Create Image Instance
	 *
	 * @param $file_path
	 * @param null $extension
	 * @return ImageProviderContract
	 */
	public function create($file_path, $extension = null) :ImageProviderContract
	{
		$this->instance = new Image($file_path, $extension);
		return $this;
	}

	/**
	 * Set Output Format Options
	 *
	 * @param string $extension
	 * @param int|null $quality
	 * @param int|null $filter
	 * @return ImageProviderContract
	 */
	public function setOutputFormat(string $extension, int $quality = null, int $filter = null) :ImageProviderContract
	{
		$this->instance->setOutputFormat($extension, $quality, $filter);
		return $this;
	}

	/**
	 * Set Save Options
	 *
	 * @param string $name
	 * @param string $path
	 * @return ImageProviderContract
	 */
	public function setSaveOptions(string $name, string $path) :ImageProviderContract
	{
		$this->instance->setSaveOptions($name, $path);
		return $this;
	}

	/**
	 * Save Image
	 *
	 * @param bool $keep_instance
	 * @return string
	 */
	public function save($keep_instance = false) :string
	{
		return $this->instance->export($keep_instance);
	}

	/**
	 * Save Image
	 *
	 * @param $width
	 * @param null $height
	 * @param bool $preserve_aspect
	 * @return ImageProviderContract
	 */
	public function createThumbnail($width, $height = null, $preserve_aspect = false) :ImageProviderContract
	{
		$this->instance->createThumbnail($width, $height, $preserve_aspect);
		return $this;
	}

	/**
	 * Destroy Image instance
	 */
	public function destroy() {
		$this->instance->destroy();	
	}
}