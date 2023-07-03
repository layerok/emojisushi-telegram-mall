<?php namespace System\Helpers;

use System\Classes\PresetManager;

/**
 * Presets
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
class Preset
{
    /**
     * icons returns list of available system icons
     */
    public static function icons(): array
    {
        return PresetManager::instance()->getPreset('icons');
    }

    /**
     * locales returns list of available locales
     */
    public static function locales(): array
    {
        return PresetManager::instance()->getPreset('locales');
    }

    /**
     * localeFlags returns list of available locales with flag icons
     */
    public static function localeIcons(): array
    {
        return PresetManager::instance()->getPreset('localeIcons');
    }
}
