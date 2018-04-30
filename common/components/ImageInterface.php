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

    public function hasImage() : bool;

    public function getImageSrc() : string;

    public function getDemoPath() : string;
}
