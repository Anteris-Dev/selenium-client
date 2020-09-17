<?php

namespace Anteris\Selenium\Client\Factories;

use Anteris\Selenium\Browser\Exceptions\InvalidDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class DriverFactory
{
    /**
     * Handles the creation of a new Selenium driver based on defaults.
     */
    public static function create(
        $driver = 'gecko',
        $host = 'http://localhost:4444/wd/hub'
    ): RemoteWebDriver {
        /**
         * In this section, we determine the desired capabilities of the
         * browser based on the driver used.
         */
        $desiredCapabilities = null;

        if ($driver == 'chrome') {
            $desiredCapabilities = DesiredCapabilities::chrome();
        } elseif ($driver == 'gecko') {
            $desiredCapabilities = DesiredCapabilities::firefox();
        } else {
            throw new InvalidDriverException("Unknown driver $driver!");
        }

        /**
         * Next we setup the Selenium client
         */
        return RemoteWebDriver::create($host, $desiredCapabilities);
    }
}
