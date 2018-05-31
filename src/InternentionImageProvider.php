<?php

/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages;

use Saad\ModelImages\Contracts\ImageProviderContract;
use Intervention\Image\ImageManager;

class InterventionImageProvider implements ImageProviderContract {

	/**
	 * Image Instance
	 * 
	 * @var Image
	 */
	protected $instance;

	/**
	 * @var string
	 */
	protected $save_extension;

	/**
	 * @var integer
	 */
	protected $save_quality;

	/**
	 * @var integer
	 */
	protected $save_filter;

	/**
	 * @var string
	 */
	protected $save_as;


	/**
	 * Create Image Instance
	 *
	 * @param $file_path
	 * @param null $extension
	 * @return ImageProviderContract
	 */
	public function create($file_path, $extension = null) :ImageProviderContract
	{
		// create an image manager instance with favored driver
		$manager = new ImageManager();
		$this->instance = $manager->make($file_path);
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
		$this->save_extension = $extension;
		$this->save_quality = $quality;
		$this->save_filter = $filter;

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
		$this->save_as = $path . $name . '.' . $this->save_extension;
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
		return $this->instance->save($this->save_as, $this->save_quality);
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
		$this->instance->resize($width, $height, function($constraint) use ($preserve_aspect) {
			if ($preserve_aspect) {
				$constraint->aspectRatio();
			}

			$constraint->upsize();
		});
		return $this;
	}

	/**
	 * Destroy Image instance
	 */
	public function destroy() {
		$this->instance->destroy();	
	}
}