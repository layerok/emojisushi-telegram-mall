<?php namespace GoTech\Webp\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'awebsome_webp_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var array Validation rules
     */
    protected $rules = [];
}
