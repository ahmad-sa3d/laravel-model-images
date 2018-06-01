<p align="center">
	<img src="https://laravel.com/assets/img/components/logo-laravel.svg">
	<h1 align="center">Model Images</h1>
</p>

<p align="center">
<a href="https://packagist.org/packages/saad/json-response-builder"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

<p>
    this package make saving (uploading or from storage) images with any set of dimensions and attachs them to you model is very easy
    just a few lines of code
    <br>
    for any customizations also have the full control of saving process
</p>

### Dependencies
this library uses two providers one of intervention/image library and the other saad/image (My Package) for image manipulation 
the default provider is my package (saad/image) but you can switch between them as you wish and also you can create your own driver if you wish and use it.

### Install
``` bash
	composer require saad/laravel-model-images
```


Basic Usage
---

#### 1- Model Setup
* assume we have a model like User model has a string field called `image`
this field will contains his profile image
	
	all we need to do is in User Model File `User.php` we need to do:
	 
	 1. implement `Saad\ModelImages\Contracts\ImageableContract`
	 2. use trait `Saad\ModelImages\Traits\HasImages`
	 3. define image fields for this model via defining a static method `imageableFields()`
	 which will return array of image fields for the model

	
``` php

// User.php File
class User extends Autheticatable implements ImageableContract
{
    use HasImages;
    
    public static function imageableFields() {
        return ['image'];
    }
}

```

#### 2- Store Images:
Now to set user profile image, assume in profile controller we have a method called `uploadUserProfile`
responds to upload image request, and another method `setUserProfile` to set profile image from storage image file

``` php

// ProfilesController.php File
class ProfilesController
{		
    // Save Image From Upload Request    
    public function setUserProfile(Request $request, User $user) {
        // You can Validate
        $image = $request->file('image');
        if ($image) {
            $user->image = $image;
        }
        
        $user->save();
    }
    
    // Set Image from image file on storage
    public function setUserProfile() {
        $user = User::find(1);
        $user->image = storage_path('app/images/profile.png');
        $user->save();
    }
}
```


#### 3- Public link
OK, for now we stored our user profile image, so how we can get our user profile image public link
<br>
this is very easy just use `$user->getImagePublicLink()` it will get profile photo url
<br>
by default all images will be stored inside `public` directory in the following directory `images/uploads`
<br>
> this library uses the convention naming *__get`ImageField`PublicLink()__*
> <br>
so if we have image field called `profile_photo` the method will be `getProfilePhotoPublicLink()`

### Customization
---

#### 1- Multiple image sizes:

to set custom sizes like thumbnails or different dimensions for your uploaded images
all you need is to define  *__`imageField`ImageSizes()__* method on your model

``` php
		
// User.php File
class User extends Autheticatable implements ImageableContract
{
    use HasImages;
    ...
    public function imageImageSizes() {
        return [
            [512, 512], // Full size version
            [256, 256], // Thumbnail version
            [46, 46],   // Thumbnail version
        ];
    }
}
		
```

> __Get Public links__ <br>
`$user->getImagePublicLink()` => for 512x512 <br>
`$user->getImagePublicLink('256x256')` => for 256x256 <br>
`$user->getImagePublicLink('46x46')` => for 46x46 <br><br>
Or yo can use general method `getPublicLink($field)` <br>
`$user->getPublicLink('image')` => for 512x512 <br>
`$user->getPublicLink('image', '256x256')` => for 256x256 <br>
`$user->getPublicLink('image', '46x46')` => for 46x46 <br>

#### 2- Custom save directory:

to set custom save directory (within public folder) define method <br>
> `imageGeneralSavePath()` this method will set save path for all model image fields <br>
> <br>
> *__image`FieldName`SavePath__* the naming convention used to set different paths for each field


``` php
		
// User.php File
class User extends Autheticatable implements ImageableContract
{
    use HasImages;
    ...
    // path for saving user profile images
    public function imageImageSavePath() :string
    {
        return 'images/users/profile';
    }
    
    // user model default saving directory for all model image fields if any
    public function imageGeneralSavePath() :string
    {
        return 'images/users';
    }
}
		
```

#### 3- Custom save options:

you can control saving format, quality and png filters by defining those methods:<br>
> `imageSaveExtension()` defines image format default `jpg` <br>
> `imageSaveQuality()` defines image quality default `70` for `jpg` <br>
>> for jpg quality will be from `0 to 100` `low to high` quality<br>
>> for png quality will be from `0 to 9` `high to low` quality<br>

> `imagePNGFilter()` defines png image filter default `PNG_NO_FILTER` <br>

``` php
		
// User.php File
class User extends Autheticatable implements ImageableContract
{
    use HasImages;
    ...
    public function imageSaveExtension() :string
    {
        return 'png';
    }
    
    public function imageSaveQuality() :int
    {
        return 7;
    }
    
    public function imagePNGFilter() :int
    {
        return PNG_ALL_FILTERS;
    }
}
		
```


#### 4- Custom save provider:

this package shipped with two image manipulation providers:<br>
> __Providers:__ <br>
> `SaadImageProvider` this is the default provider, it uses my image package `saad/image` for image manipulation process <br>
> `InterventionImageProvider` provider for famous package `intervention/image` for image manipulation process <br>

> __Define Custom Provider:__ <br>
> you can define your custom provider class by implementing this interface
`Saad\ModelImages\Contracts\ImageProviderContract`

> __Change Provider__ <br>
you can change driver be defining `imageSaveProvider()` method in your model, which should return the provider instance.<br>


> __Note:__ <br>
>
> There are difference between how intervention and my image library handle resizing images when width and height are defined and differs from original aspect<br>
>
> __saad/image__ resize images to new dimensions by centering original image then resize and crop if necessary<br>
>
> __intervention/image__ will stretch image to new size or will preserve aspect so it will respect one dimension and auto set the other according to original aspect 
>
> if this note is not clear, you can try both providers and see.

``` php
		
// User.php File
class User extends Autheticatable implements ImageableContract
{
    use HasImages;
    ...
    public function imageSaveProvider() :ImageProviderContract
    {
        return new InterventionImageProvider;
    }

}
		
```


#### 4- Default image:

Each model image field should have a default image, so consider the following cases:<br>
> 1-`Record does not have image`
> when we try to get an image for a record which does not have an image attached yet <br>
>
> 2- `Record has image but file deleted or not exists`
> when we try to get an image for a record but this image file is deleted or not exists <br>

for these reasons we should define default image for each field, and this package
will automatically return this default image if one of the previous cases happens

__To set Default Image:__
defining a default image is the same process as attaching image to the model, the only difference is we tell the model that we are defining default image <br>
by calling the static method `settingDefaultImage()` on the model ex: `User::settingDefaultImage()` <br>
here we can see in action:

``` php

// ProfilesController.php File
class ProfilesController
{		
    // Set image field default image
    public function setDefaultProfileImage() {
        // telling User model we are defining default image
        User::settingDefaultImage();
        
        // assign default image
        $user = new User;
        $user->image = storage_path('app/images/default_profile.png');
    }
}
```
