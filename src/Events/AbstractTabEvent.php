<?php

namespace Anteris\Selenium\Client\Events;

use Anteris\Selenium\Client\Browser;

abstract class AbstractTabEvent extends AbstractBrowserEvent
{
    /** @var string The window handle for the tab we are interacting with. */
    private string $tabHandle;

    /**
     * Sets the handle for the tab.
     */
    public function __construct(Browser $browser, string $tabHandle)
    {
        $this->tabHandle = $tabHandle;

        parent::__construct($browser);
    }

    /**
     * Returns the window handle for the tab created.
     */
    public function getTab()
    {
        return $this->tabHandle;
    }
}
