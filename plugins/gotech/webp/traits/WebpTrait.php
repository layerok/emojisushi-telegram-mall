<?php namespace GoTech\Webp\Traits;

use Illuminate\Http\UploadedFile;
use GoTech\Webp\Models\Settings as PluginConfig;
trait WebpTrait
{
    /**
     * @var UploadedFile
     */
    protected $image;

    /**
     * @var int
     */
    protected $quality;

    /**
     * @param UploadedFile $image
     * @return WebpTrait
     */
    public function make(UploadedFile $image): self
    {
        $this->quality = PluginConfig::get('default_quality');
        $this->image = $image;

        return $this;
    }

    /**
     * @param $quality
     * @return WebpTrait
     */
    public function quality($quality): self
    {
        $this->quality = $quality;

        return $this;
    }
}