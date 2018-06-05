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
	 * Backup Image Resource
	 *
	 * @return void
	 */
	public function backup() :void
	{
		$this->instance->backup();
	}

	/**
	 * Rest Image Resource from a backup
	 *
	 * @return void
	 */
	public function reset() :void
	{
		$this->instance->reset();
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
	 * @param string $name might be with or without extension
	 * @param string $path
	 * @return ImageProviderContract
	 */
	public function setSaveOptions(string $name, string $path) :ImageProviderContract
	{
		// Check Path if exists or not
		$this->createMissingPath($path);

		$this->save_as = $path . $name;
		if (! preg_match('/^.*\.[\w]{3,4}$/', $name)) {
			$this->save_as .= '.' . $this->save_extension;
		}
		return $this;
	}

	/**
	 * Check Path and create Missing Directories if necessary
	 *
	 * @param $path
	 */
	private function createMissingPath($path) {
		if (! file_exists($path)) {
			mkdir($path, 0755, true);
		}
	}

	/**
	 * Save Image
	 *
	 * @param bool $keep_instance
	 * @return string
	 */
	public function save($keep_instance = false) :string
	{
		$this->instance->save($this->save_as, $this->save_quality);
		return basename($this->save_as);
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