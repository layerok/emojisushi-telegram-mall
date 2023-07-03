<?php namespace GoTech\Webp;

use Agent;
use Cache;
use Storage;
use Route;
use Response;
use System\Classes\PluginBase;

use System\Models\File;
use Illuminate\Http\UploadedFile;
use \GoTech\Webp\Classes\Webp;
use \GoTech\Webp\Models\Settings as PluginConfig;

class Plugin extends PluginBase
{
    public $require = [
        'Pikanji.Agent'
    ];

    public function boot()
    {
        // Permita pasar un archivo
        // Permita pasar una url
        Route::get('/caniuse/webp', function () {

            $os      = Agent::platform();
            $browser = Agent::browser();
            $version = Agent::version($browser);
            
            return Response::view('gotech.webp::hello_world', [
                'os'        => $os,
                'browser'   => $browser,
                'version'   => $version,
                'image'     => "/plugins/gotech/webp/assets/img/test.png",
            ]);
        });
    }

    public function registerComponents()
    {
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Webp',
                'description' => 'WebP is a modern image format that provides superior lossless and lossy compression for images on the web.',
                'category'    => 'Webp',
                'icon'        => 'icon-map-signs',
                'class'       => 'GoTech\Webp\Models\Settings',
                'order'       => 600,
                'permissions' => ['gotech.webp.settings'],
            ]
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'webp' => [$this, 'webp'],
                'bwebp' => [$this, 'bwebp'], // Force backend controll
                'fwebp' => [$this, 'fwebp'], // Force frontend controll
            ],
        ];
    }

    public function webp($file, $quality = null, $imgInfo = false) 
    {
        $webp = new Webp;

        if(is_null($quality)) {
            $quality = PluginConfig::get('default_quality', 100);
        }

        $result = $webp->file($file, $quality, $imgInfo, PluginConfig::get('backend_compatibility', 100));

        return $result;
    }

    public function bwebp($file, $quality = null, $imgInfo = false) 
    {
        $webp = new Webp;

        if(is_null($quality)) {
            $quality = PluginConfig::get('default_quality', 100);
        }

        $result = $webp->file($file, $quality, $imgInfo, true);

        return $result;
    }
    
    public function fwebp($file, $quality = null, $imgInfo = false) 
    {
        $webp = new Webp;

        if(is_null($quality)) {
            $quality = PluginConfig::get('default_quality', 100);
        }

        $result = $webp->file($file, $quality, $imgInfo, false);

        return $result;
    }
}
