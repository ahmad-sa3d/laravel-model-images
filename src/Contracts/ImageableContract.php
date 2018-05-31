<?php

/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages\Contracts;

interface ImageableContract {

	/**
	 * Model Image fields
	 * 
	 * @return array
	 */
	public static function imageableFields() :array;

	/**
	 * Set setting default image status
	 *
	 * @param bool $bool
	 */
	public static function settingDefaultImage(bool $bool = true);

	/**
	 * Check if setting default image
	 *
	 * @return  boolean
	 */
	public function isSettingDefaultImage() :bool;

	/**
	 * Get Image Supported Formats
	 * 
	 * @return array
	 */
	public function imageSupportedFormats() :array;

	/**
	 * Check if  storage image exists
	 *
	 * @param $image
	 * @return bool
	 */
	public function imageExists($image) :bool;

	/**
	 * Get Save Extension
	 * 
	 * @return string
	 */
	public function imageSaveExtension() :string;

	/**
	 * Get Save Quality
	 * 
	 * @return integer
	 */
	public function imageSaveQuality() :int;

	/**
	 * Get Save Filter
	 * 
	 * @return integer
	 */
	public function imagePNGFilter() :int;

	/**
	 * Get Save Name
	 *
	 * @param $field
	 * @return string
	 */
	public function getSaveName($field) :string;

	/**
	 * Get General Save Name
	 *
	 * @return string
	 */
	public function imageGeneralSaveName() :string;

	/**
	 * Get General Save Path
	 *
	 * @param $field
	 * @return string
	 */
	public function getSavePath($field) :string;

	/**
	 * Get General Save Path
	 *
	 * @return string
	 */
	public function imageGeneralSavePath() :string;

	/**
	 * Get Image Save Sizes
	 *
	 * @param $field
	 * @return array
	 */
	public function getSaveSizes($field) :array;

	/**
	 * Get Image Save Provider
	 * 
	 * @return ImageProviderContract
	 */
	public function imageSaveProvider() :ImageProviderContract;
}