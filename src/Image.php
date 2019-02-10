<?php

namespace Soumen\Image;

use File;
use Storage;
use Intervention\Image\ImageManager;

class Image
{
    /**
     * The image file.
     *
     * @author Soumen Dey
     */
    protected $file;

    /**
     * The location that will be used to store the files.
     *
     * @author Soumen Dey
     */
    protected $storage = null;

    /**
     * The image quality to be used while storing the image.
     *
     * @author Soumen Dey
     */
    protected $quality = 50;

    /**
     * The default encoding to be used.
     *
     * @author Soumen Dey
     */
    protected $encoding = 'jpeg';

    /**
     * The original filename as it was uploaded.
     *
     * @author Soumen Dey
     */
    protected $original_filename;

    /**
     * The file extension.
     *
     * @author Soumen Dey
     */
    protected $extension;

    /**
     * The original filepath as it was uploaded.
     *
     * @author Soumen Dey
     */
    protected $original_filepath;

    /**
     * The mimetype.
     *
     * @author Soumen Dey
     */
    protected $mimetype;

    /**
     * The image filename after storing.
     *
     * @author Soumen Dey
     */
    protected $filename;

    /**
     * The thumbnail image.
     *
     * @author Soumen Dey
     */
    protected $thumbnail = null;

    /**
     * The thumbnail name prefix.
     *
     * @author Soumen Dey
     */
    protected $thumbnail_prefix = 'thumb_';

    /**
     * The storage for thumbnail.
     *
     * @author Soumen Dey
     */
    protected $thumbnail_storage = null;

    /**
     * Determine if the image should have thumbnail.
     *
     * @author Soumen Dey
     */
    protected $has_thumbnail = true;

    /**
     * The encoded file.
     *
     * @author Soumen Dey
     */
    protected $encoded_file;

    /**
     * The image driver.
     *
     * @author Soumen Dey
     */
    protected $driver = null;

    public function __construct($file = null)
    {
        if ($file) {
            $this->file = $file;
            $this->setFileName();
            $this->extractImageDetailsFromMetaData();
        }
    }

    /**
     * Set the file to the specified file.
     *
     * @author Soumen Dey
     */
    public function set($file)
    {
        $this->setFile($file);
        $this->setFileName();
        $this->extractImageDetailsFromMetaData();

        return $this;
    }

    /**
     * Encodes the image and generates the thumbnail.
     *
     * @return Self
     * @author Soumen Dey
     */
    public function process()
    {
        $this->encode();
        $this->generateThumbnail();

        return $this;
    }

    /**
     * Stores the image in the storage.
     *
     * @author Soumen Dey
     */
    public function store()
    {
       $this->putFile();

       if ($this->has_thumbnail) {
           $this->putThumbnail();
       }

       return $this;
    }

    /**
     * Get details about the file.
     *
     * @return Self
     * @author Soumen Dey
     */
    public function extractImageDetailsFromMetaData()
    {
        $this->original_filename = $this->file->getClientOriginalName();
        $this->original_filepath = $this->file->path();
        $this->extension = $this->file->extension();
        $this->mimetype = $this->file->getMimeType();
        
        return $this;
    }
    
    /**
     * Encodes and compresses the image.
     *
     * @author Soumen Dey
     */
    public function encode()
    {
        $image = new ImageManager(['driver' => $this->getDriver()]);

        $this->encoded_file = $image->make($this->original_filepath)->stream(
            $this->getEncoding(), $this->getQuality()
        );
        
        return $this;
    }

    /**
     * Generates thumbnail for the specified image.
     *
     * @author Soumen Dey
     */
    public function generateThumbnail()
    {
        $this->has_thumbnail = true;

        $image = new ImageManager(['driver' => $this->getDriver()]);

        $this->thumbnail = $image->make($this->original_filepath)->resize(350, 350, function ($constraint) {
            $constraint->aspectRatio();
        })->stream($this->getEncoding(), $this->getQuality());

        return $this;
    }

    /**
     * Put the file in the storage.
     *
     * @author Soumen Dey
     */
    public function putFile()
    {
        if (!config('laravel-image.store_to_disk')) {
            Storage::put($this->getStorage() . '/' . $this->filename, $this->encoded_file);
        } else {
            // store the image in the storage
            Storage::disk($this->getStorage())->put($this->filename, $this->encoded_file);
        }

        
        return $this;
    }

    /**
     * Store the thumbnail in the storage.
     *
     * @author Soumen Dey
     */
    public function putThumbnail()
    {
        if (!$this->has_thumbnail) {
            return $this;
        }

        if (!config('laravel-image.store_to_disk')) {
            Storage::put(
                $this->getThumbnailStorage() . '/' . $this->thumbnail_prefix . $this->filename, 
                $this->thumbnail
            );
        } else {
            Storage::disk($this->getThumbnailStorage())->put(
                $this->thumbnail_prefix . $this->filename, $this->thumbnail
            );
        }

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Getters and Setters
    |--------------------------------------------------------------------------
    */

    /* File */

    /**
     * Get the file.
     *
     * @author Soumen Dey
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the file.
     *
     * @author Soumen Dey
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /* Storage */

    /**
     * Get the location of the file storage.
     *
     * @return String
     * @author Soumen Dey
     */
    public function getStorage()
    {
        if ($this->storage) {
            return $this->storage;
        }

        return config('laravel-image.image_storage');
    }

    /**
     * Get the location of the file storage.
     *
     * @return String
     * @author Soumen Dey
     */
    public function getThumbnailStorage()
    {
        if ($this->thumbnail_storage) {
            return $this->thumbnail_storage;
        }

        return config('laravel-image.thumbnail_storage');
    }

    /**
     * Set the location of the file storage.
     *
     * @author Soumen Dey
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        
        return $this;
    }
    
    /**
     * Set the location of the file storage.
     *
     * @author Soumen Dey
     */
    public function setThumbnailStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /* Encoding */

    /**
     * Get the encoding to be used for the file.
     *
     * @return String
     * @author Soumen Dey
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set the encoding to be used for the file.
     *
     * @author Soumen Dey
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        $this->setFilename();

        return $this;
    }

    /* Quality */

    /**
     * Get the quality of the file to be uploaded.
     *
     * @return String
     * @author Soumen Dey
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Set the quality of the file to be uploaded.
     *
     * @author Soumen Dey
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /* Driver */

    /**
     * Set the image driver.
     *
     * @author Soumen Dey
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get the image driver.
     *
     * @author Soumen Dey
     */
    public function getDriver()
    {
        if (!$this->driver) {
            $driver = config('laravel-image.driver');
            $this->driver = $driver;
            return $driver;
        }

        return $this->driver;
    }

    /**
     * Set the file name.
     *
     * @author Soumen Dey
     */
    public function setFilename($filename = null)
    {
        $this->filename = ($filename) 
                            ? $filename 
                            : md5(openssl_random_pseudo_bytes(32) . mt_rand(100, 100000)) . '.' . $this->getEncoding();

        return $this;
    }

    /**
     * Set the file name.
     *
     * @author Soumen Dey
     */
    public function getFilename()
    {
        return $this->filename;
    }
}