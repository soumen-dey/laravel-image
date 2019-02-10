# Laravel Image

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

A package to encode and store your Uploaded Images for Laravel 5.6 and above.

## Installation

Via Composer

This package depends on [intervention/image](https://github.com/intervention/image)

``` bash
$ composer require soumen-dey/laravel-image
```

Laravel Auto-Discovery will automatically detect and add the Service Provider and Alias for the package. If it doesn't, do the following:

In the ```$providers``` array add the service providers for this package.

``` php
Soumen\Image\ImageServiceProvider::class
```

Add the facade of this package to the ```$aliases``` array.

``` php
'Image' => Soumen\Image\Facades\Image::class
```

## Configuration

By default this packages uses GD library for processing the images, to change this behaviour and also to modify the default storage options, publish the configuration file for this package.

``` bash
$ php artisan vendor:publish --provider="Soumen\Image\ImageServiceProvider"
```

## Usage

``` php
use Soumen\Image

$file = $request->file('file');

// This will initialize and extract meta-data from the image
$image = new Image($file);

// This will encode the image and generate thumbnail
$image->process();

// Store the image in the storage
$image->store();
```

You can also use method chaining to write the above code in one line.

``` php
$image = (new Image($file))->process()->store();
```

You can also specify parameters.

``` php
$image = new Image($file);
$image->setQuality(90); // the jpeg image quality, default is 50
$image->setEncoding('png'); // default is jpeg
$image->setStorage('images'); // set the storage
$image->generateThumbnail(); // generates the thumbnail
$image->encode();
$image->store();
```

Do not use ```$image->process()``` if you wish specify parameters.

You can also use method chaining on the above code.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/soumen/image.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/soumen/image.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/soumen/image/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/soumen-dey/laravel-image
[link-downloads]: https://packagist.org/packages/soumen/laravel-image
[link-travis]: https://travis-ci.org/soumen-dey/laravel-image
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/soumen-dey
[link-contributors]: ../../contributors
