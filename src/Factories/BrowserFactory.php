<?php

namespace Anteris\Selenium\Client\Factories;

use Anteris\Selenium\Client\Browser;

class BrowserFactory
{
    /**
     * An easy to startup but opinionated version of our Browser. Creates the
     * browser with a Firefox driver and default event emitter.
     */
    public static function create(): Browser
    {
        return new Browser(
            DriverFactory::create(),
            EventFactory::create()
        );
    }
}
