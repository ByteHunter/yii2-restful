<?php
namespace common\components;

interface ImageInterface
{
    /**
     * Must return attribute name used to store image source
     * Example: `return 'icon_src'`
     * @return string
     */
    public function imageAttribute() : string;
    
    /**
     * Must return non-absolute directory where images will be stored
     * Example: `return 'manufacturer'`
     * @return string
     */
    public function imagePath() :  string;
    
    /**
     * Must return non-associative array containing with and height.
     * Example: `return [64, 64]`
     * @return array|NULL
     */
    public function getImageMaxSize() : array;
    
    public function hasImage() : bool;
    
    public function getLocalImageSource() : ?string;
    
    public function getImageSize() : ?array;
    
    public function validateImageSize() : bool;
}