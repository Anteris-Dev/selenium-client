<?php

namespace Anteris\Selenium\Client\Events;

use Anteris\Selenium\Client\Browser;
use League\Event\AbstractEvent;

abstract class AbstractBrowserEvent extends AbstractEvent
{
    /** @var Browser The browser instance we are currently running. */
    private Browser $browser;

    /**
     * Sets the current browser context.
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * Returns the current browser context.
     */
    public function getBrowser(): Browser
    {
        return $this->browser;
    }
}
