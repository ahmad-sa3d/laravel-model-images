<?php

namespace Saad\ModelImages\Contracts;

use Saad\ModelImages\Contracts\ImageProviderContract;

interface ImageableContract {

	/**
	 * Model Imageable fields
	 * 
	 * @return array
	 */
	public static function imageableFields() :array;

	/**
	 * Set setting default image status
	 * 
	 * @param  boolean $bool [description]
	 */
	public static function settingDefaultImage(bool $bool = true);

	/**
	 * Check if setting default image
	 * 
	 * @return  boolean [description]
	 */
	public function isSettingDefaultImage() :bool;

	/**
	 * Get Image Supported Formats
	 * 
	 * @return [type] [description]
	 */
	public function imageSupportedFormats() :array;

	/**
	 * Check if  storage image exists
	 * 
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
	public function imageSaveFilter() :int;

	/**
	 * Get Save Name
	 * 
	 * @return integer
	 */
	public function getSaveName($field) :string;

	/**
	 * Get General Save Name
	 * 
	 * @return integer
	 */
	public function imageGeneralSaveName() :string;

	/**
	 * Get General Save Path
	 * 
	 * @return integer
	 */
	public function getSavePath($field) :string;

	/**
	 * Get General Save Path
	 * 
	 * @return integer
	 */
	public function imageGeneralSavePath() :string;

	/**
	 * Get Image Save Sizes
	 * 
	 * @return integer
	 */
	public function getSaveSizes($field) :array;

	/**
	 * Get Image Save Provider
	 * 
	 * @return ImageProviderContract
	 */
	public function imageSaveProvider() :ImageProviderContract;
}