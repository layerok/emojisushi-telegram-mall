<?php namespace Layerok\BaseCode;

use System\Classes\PluginBase;
use Validator;
/**
 * BaseCode Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = [];
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'BaseCode',
            'description' => 'No description provided yet...',
            'author'      => 'Layerok',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Validator::extend('phoneUa', function($attribute, $value, $parameters) {
            $regex = "/^(((\+)(38))\s?)(([0-9]{3})|(\([0-9]{3}\)))(\-|\s)?(([0-9]{3})(\-|\s)?
        ([0-9]{2})(\-|\s)?([0-9]{2})|([0-9]{2})(\-|\s)?([0-9]{2})(\-|\s)?
        ([0-9]{3})|([0-9]{2})(\-|\s)?([0-9]{3})(\-|\s)?([0-9]{2}))$/";

            return preg_match($regex, $value);
        });

    }

}
