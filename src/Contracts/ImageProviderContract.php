<?php

/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages\Contracts;

interface ImageProviderContract {

	/**
	 * Create Image Instance
	 *
	 * @param string $file_path image file path
	 * @param string $extension image file extension fallback
	 * @return ImageProviderContract
	 */
	public function create($file_path, $extension = null) :ImageProviderContract;

	/**
	 * Backup Image Resource
	 *
	 * @return void
	 */
	public function backup() :void;

	/**
	 * Reset Image Resource from a backup
	 *
	 * @return void
	 */
	public function reset() :void;

	/**
	 * Set Output Format Options
	 *
	 * @param string $extension save base extension
	 * @param int $quality save quality
	 * @param int $filter output filter
	 * @return ImageProviderContract
	 */
	public function setOutputFormat(string $extension, int $quality = null, int $filter = null) :ImageProviderContract;

	/**
	 * Set Save Options
	 *
	 * @param string $name save base name
	 * @param string $path save directory
	 * @return ImageProviderContract
	 */
	public function setSaveOptions(string $name, string $path) :ImageProviderContract;

	/**
	 * Save Image
	 *
	 * @param bool $keep_instance
	 * @return string
	 */
	public function save($keep_instance = false) :string;

	/**
	 * Save Image
	 *
	 * @param int $width Thumb width
	 * @param int $height Thumb height
	 * @param bool $preserve_aspect
	 * @return ImageProviderContract
	 */
	public function createThumbnail($width, $height = null, $preserve_aspect = false) :ImageProviderContract;

	/**
	 * Destroy Image instance
	 */
	public function destroy();
}