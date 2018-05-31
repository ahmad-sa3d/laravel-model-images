<?php

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
	 * @return
	 */
	public function create($file_path, $extension = null) :ImageProviderContract
	{
		$this->instance = new Image($file_path, $extension);
		return $this;
	}

	/**
	 * Set Output Format Options
	 * 
	 * @param string $extension save base extension
	 * @param int $quality save quality
	 * @param string $filter output filter
	 * @return
	 */
	public function setOutputFormat(string $extension, int $quality = null, int $filter = null) :ImageProviderContract
	{
		$this->instance->setOutputFormat($extension, $quality, $filter);
		return $this;
	}

	/**
	 * Set Save Options
	 * 
	 * @param string $name save base name
	 * @param string $path save directory
	 * @return
	 */
	public function setSaveOptions(string $name, string $path) :ImageProviderContract
	{
		$this->instance->setSaveOptions($name, $path);
		return $this;
	}

	/**
	 * Save Image
	 * 
	 * @return
	 */
	public function save($keep_instance = false) :string
	{
		return $this->instance->export($keep_instance);
	}

	/**
	 * Save Image
	 * 
	 * @param int $width Thumb width
	 * @param int $height Thumb height
	 * @param int $height Thumb height
	 * @return
	 */
	public function createThumbnail($width, $height = null, $preserve_aspect = false) :ImageProviderContract
	{
		$this->instance->createThumbnail($width, $height, $preserve_aspect);
		return $this;
	}

	/**
	 * Destroy Image instance
	 * 
	 * @return
	 */
	public function destroy() {
		$this->instance->destroy();	
	}
}