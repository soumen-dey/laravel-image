<?php

return [

    /*
     * The driver that will be used to create images. Can be set to gd or imagick.
     */
    'driver' => 'gd',

    /*
     * Determines if the images should be stored to disk.
     */
    'store_to_disk' => false,

    /*
     * The name of the disk. If store_to_disk is set to false, then this value will be used
     * as folder name.
     */
    'image_storage' => 'images',

    /*
     * The name of the disk. If store_to_disk is set to false, then this value will be used
     * as folder name.
     */
    'thumbnail_storage' => 'thumbnails',

];