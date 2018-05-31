<?php

namespace Saad\ModelImages\Contracts;

interface ImageProviderContract {
	/**
	 * Create Image Instance
	 * 
	 * @param string $file_path image file path
	 * @param string $extension image file extension fallback
	 * @return
	 */
	public function create($file_path, $extension = null) :ImageProviderContract;

	/**
	 * Set Output Format Options
	 * 
	 * @param string $extension save base extension
	 * @param int $quality save quality
	 * @param string $filter output filter
	 * @return
	 */
	public function setOutputFormat(string $extension, int $quality = null, int $filter = null) :ImageProviderContract;

	/**
	 * Set Save Options
	 * 
	 * @param string $name save base name
	 * @param string $path save directory
	 * @return
	 */
	public function setSaveOptions(string $name, string $path) :ImageProviderContract;

	/**
	 * Save Image
	 * 
	 * @return
	 */
	public function save($keep_instance = false) :string;

	/**
	 * Save Image
	 * 
	 * @param int $width Thumb width
	 * @param int $height Thumb height
	 * @param int $height Thumb height
	 * @return
	 */
	public function createThumbnail($width, $height = null, $preserve_aspect = false) :ImageProviderContract;

	/**
	 * Destroy Image instance
	 * 
	 * @return
	 */
	public function destroy();

}