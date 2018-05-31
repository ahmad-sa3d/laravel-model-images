<?php
/**
 * @package  saad/laravel-model-images
 *
 * @author Ahmed Saad <a7mad.sa3d.2014@gmail.com>
 * @license MIT MIT
 */

namespace Saad\ModelImages;

use Saad\ModelImages\Contracts\ImageProviderContract;
use Saad\ModelImages\Contracts\ImageableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use BadMethodCallException;

class ImageSaver {

	/**
	 * Model
	 * 
	 * @var Model
	 */
	protected $model;

	/**
	 * Image Field
	 * 
	 * @var string
	 */
	protected $field;

	/**
	 * Image File
	 * 
	 * @var UploadedFile|string
	 */
	protected $image;

	/**
	 * Image Extension
	 * 
	 * @var string
	 */
	protected $extension;

	/**
	 * Is Uploaded Image
	 * 
	 * @var string
	 */
	protected $is_uploaded;

	/**
	 * Image Provider
	 * 
	 * @var ImageProviderContract
	 */
	protected $image_provider;


	/**
	 * ImageSaver constructor.
	 * @param ImageableContract $model
	 * @param $field
	 * @param null $image
	 */
	public function __construct(ImageableContract $model, $field, $image = null) {
		$this->model = $model;
		$this->field = $field;
		$this->image = $image;

		// return if no Image (Delete Mode)
		if (is_null($image)) {
			return;
		}

		if ($image instanceof UploadedFile) {
			$this->is_uploaded = true;
			$this->setupUploadedImage();
		} else if ($this->model->imageExists($image)){
			$this->setupStorageImage();
		} else {
			throw new InvalidArgumentException(sprintf('%s::%s() image "%s" not exists', static::class, __METHOD__, $image));
		}

		$this->setImageProvider($this->model->imageSaveProvider());
	}

	/**
	 * Set Image Provider
	 *
	 * @param ImageProviderContract $provider
	 */
	public function setImageProvider(ImageProviderContract $provider) {
		$this->image_provider = $provider;
	}

	/**
	 * Save Image
	 *
	 * @return mixed
	 */
	public function save() {
		if (! $this->image) {
			throw new BadMethodCallException(sprintf('%s::%s() Instance instantiated without image input (delete image mode)', static::class, __METHOD__));
		}

		$file = $this->is_uploaded ? $this->image->getPathname() : $this->image;

		$this->image_provider->create($file, $this->extension);
		$this->image_provider->setOutputFormat(
			$this->model->imageSaveExtension(),
			$this->model->imageSaveQuality(),
			$this->model->imagePNGFilter()
		);

		$name = $this->model->getSaveName($this->field);
		$path = public_path($this->model->getSavePath($this->field));
		$sizes = $this->model->getSaveSizes($this->field);

		// Saving Process
		if (empty($sizes)) {
            $save_name = $this->image_provider->setSaveOptions($name, $path)
            				->save();
        } else {
            $main_size = array_shift($sizes);
            $main = $this->getSize($main_size);
            $save_name = $this->image_provider->createThumbnail($main['w'], $main['h'], true)
                        ->setSaveOptions($name, $path)
                        ->save(true);

            foreach ($sizes as $size) {
                $size = $this->getSize($size);
                $this->image_provider->createThumbnail($size['w'], $size['h'], true)
                	->setSaveOptions(join('x', $size) . '_' . $save_name, $path . 'thumb')
                	->save(true);
            }

	        // Destroy Resource
	        $this->image_provider->destroy();
        }

        return $save_name;
	}

	/**
	 * Delete Image
	 *
	 * @param null $path
	 * @return int
	 */
    public function removeImage($path = null) {
    	$old_image = $this->model->{$this->field};
    	$path = $path ?? public_path($this->model->getSavePath($this->field));

    	$deleted_count = 0;

		if ($old_image) {
            if (file_exists($path . $old_image)) {
                unlink($path . $old_image);
                $deleted_count++;
            }

            $thumb_dir = $path . 'thumb' . DIRECTORY_SEPARATOR;
            
            if (is_dir($thumb_dir)) {
                // get cwd first, then change cwd
                $cwd = getcwd();
                chdir($thumb_dir);
                foreach (glob('*' . pathinfo($old_image)['filename'] . '*') as $file) {
                    unlink($thumb_dir . $file);
	                $deleted_count++;
                }
                // go back to original cwd
                chdir($cwd);
            }  
        }

        return $deleted_count;
    }

	/**
	 * Setup Uploaded Image
	 */
	protected function setupUploadedImage() {
		$this->extension = $this->image->extension();
		$this->checkImageSupport();
	}

	/**
	 * Setup Storage Image
	 */
	protected function setupStorageImage() {
		$arr = explode('.', $this->image);
		$this->extension = end($arr);
		$this->checkImageSupport();
	}

	/**
	 * Check Uploaded Image Format
	 *
	 * @throws InvalidArgumentException
	 */
	protected function checkImageSupport() {
		if (!in_array($this->extension, $this->model->imageSupportedFormats())) {
			throw new InvalidArgumentException(sprintf('%s::%s() image format "%s" not supported', static::class, __METHOD__, $this->extension));
		}
	}

	/**
	 * Get Width, Height values
	 * @param $size
	 * @return array
	 */
    private function getSize($size) {
        $dim = [];
        if (is_array($size)) {
            $dim['w'] = $size[0];
            $dim['h'] = isset($size[1]) ? $size[1] : $size[0];
        }
        else
        {
            $dim['w'] = $size;
            $dim['h'] = null;
        }
        return $dim;
    }
}