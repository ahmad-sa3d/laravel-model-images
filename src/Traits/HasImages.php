<?php

/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages\Traits;

use Saad\ModelImages\Contracts\ImageProviderContract;
use Saad\ModelImages\ImageSaver;
use Saad\ModelImages\SaadImageProvider;
use Illuminate\Support\Str;

trait HasImages {

	use MacroableModel;

	/**
	 * Cached Links
	 *
	 * @var array
	 */
	protected $cached_links = [];

	/**
	 * Is Setting Default Images
	 *
	 * @var bool
	 */
	protected static $is_saving_default_image;

	/**
	 * Register Mutator Macros (Dynamic Methods)
	 */
	protected static function boot() {
		parent::boot();

		foreach(static::imageableFields() as $field) {
			$studly = Str::studly($field);

			// Mutator Macro
			$mutator = 'set'.$studly.'Attribute';
			static::macro($mutator, function($value) use ($field) {
				$image_saver = new ImageSaver($this, $field, $value);
				$name = $image_saver->save();

				if (! $this->isSettingDefaultImage()) {
					// Remove Old Images
					$image_saver->removeImage();
					$this->attributes[$field] = $name;
				}
			});

			// Accessor Macro
			$accessor = 'get'.$studly.'PublicLink';
			static::macro($accessor, function($prefix = null) use ($field) {
				return $this->getPublicLink($field, $prefix);
			});

			// Remove Image Macro
			$method = 'remove'.$studly.'Image';
			static::macro($method, function($save = false) use ($field) {
				$image_deleter = new ImageSaver($this, $field);
				$deleted_images = $image_deleter->removeImage();

				$this->attributes[$field] = null;
				if ($save) {
					$this->save();
				}

				return $deleted_images;
			});
		}
	}

	/**
	 * Set setting default image status
	 *
	 * @param bool $bool
	 */
	public static function settingDefaultImage(bool $bool = true) {
		static::$is_saving_default_image = $bool;
	}

	/**
	 * Check if setting default image
	 *
	 * @return  boolean
	 */
	public function isSettingDefaultImage() :bool
	{
		return (bool) static::$is_saving_default_image;
	}

	/**
	 * Get Image Supported Formats
	 *
	 * @return array
	 */
	public function imageSupportedFormats() :array
	{
		return [
			'png', 'jpeg', 'jpg', 'gif',
		];
	}

	/**
	 * Check if  storage image exists
	 *
	 * @param $image
	 * @return bool
	 */
	public function imageExists($image) :bool
	{
		return file_exists($image);
	}

	/**
	 * Get Save Extension
	 *
	 * @return string
	 */
	public function imageSaveExtension() :string
	{
		return 'jpg';
	}

	/**
	 * Get Save Quality
	 *
	 * @return integer
	 */
	public function imageSaveQuality() :int
	{
		return 70;
	}

	/**
	 * Get Save Filter
	 *
	 * @return integer
	 */
	public function imagePNGFilter() :int
	{
		return PNG_NO_FILTER;
	}

	/**
	 * Get Save Name
	 *
	 * @param $field
	 * @return string
	 */
	public function getSaveName($field) :string
	{
		if ($this->isSettingDefaultImage()) {
			return $this->defaultImageName($field);
		}

		$base_name_method = Str::camel($field) . 'ImageSaveName';
		if (method_exists($this, $base_name_method)) {
			$name = $this->{$base_name_method}();
		} else {
			$name = $this->imageGeneralSaveName();
		}

		return $name;
	}

	/**
	 * Get Field Default Image Name
	 *
	 * @param $field
	 * @return string
	 */
	private function defaultImageName($field) :string
	{
		$class = Str::snake(class_basename($this));
		return "default_{$class}_{$field}";
	}

	/**
	 * Get General Save Name
	 *
	 * @return string
	 */
	public function imageGeneralSaveName() :string
	{
		return str_random(10) . time();
	}

	/**
	 * Get Save Path
	 *
	 * @param $field
	 * @return string
	 */
	public function getSavePath($field) :string
	{
		$path_method = Str::camel($field) . 'ImageSavePath';
		if (method_exists($this, $path_method)) {
			$path = $this->{$path_method}();
		} else {
			$path = $this->imageGeneralSavePath();
		}

		return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get Image Save Sizes
	 *
	 * @param $field
	 * @return array
	 */
	public function getSaveSizes($field) :array
	{
		$path_method = Str::camel($field) . 'ImageSizes';
		if (method_exists($this, $path_method)) {
			return $this->{$path_method}();
		} else {
			return [];
		}
	}

	/**
	 * Get General Save Name
	 *
	 * @return string
	 */
	public function imageGeneralSavePath() :string
	{
		return 'images/upload';
	}


	/**
	 * Get Provider which will manipulate image processing
	 *
	 * @return ImageProviderContract
	 */
	public function imageSaveProvider() :ImageProviderContract
	{
		return new SaadImageProvider();
	}

	/**
	 * Get Image Public Link
	 *
	 * @param $field
	 * @param null $prefix
	 * @return string
	 */
	public function getPublicLink($field, $prefix = null)
	{
		$prefix = $prefix ? 'thumb/' . $prefix . '_' : null;
		$c_key = $prefix . $field;

		if (isset($this->cached_links[$c_key])) {
			return $this->cached_links[$c_key];
		}

		$path = $this->getSavePath($field);
		$link = $path . $prefix . $this->{$field};
		if (!is_file(public_path($link))) {
			$link = $path . $prefix . $this->defaultImageName($field) . '.' . $this->imageSaveExtension();
		}

		return $this->cached_links[$c_key] = $link;
	}
}