<?php namespace GoTech\Webp\Traits;

use Agent;

trait BrowserCompatibility
{
    /**
     * isCompatibleBrowser()
     * ============================================
     * Browser compatibility rules
     * @return boolean
     */
    public function isCompatibleBrowser()
    {
        $os         = Agent::platform();
        $browser    = Agent::browser();
        $version    = str_replace(['.','-'],'', Agent::version($browser));

        // Validar safari
        if(starts_with($browser, 'Safari')) {
            
            if($version < "1402") {
                return false;
            }
        }

        return true;
    }
}
