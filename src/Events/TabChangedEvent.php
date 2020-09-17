<?php

namespace Anteris\Selenium\Client\Events;

use Anteris\Selenium\Client\Browser;

class TabChangedEvent extends AbstractBrowserEvent
{
    /** @var string Window handle of the previous tab. */
    private string $previousTabHandle;

    /** @var string Window handle of the current tab. */
    private string $currentTabHandle;

    /**
     * Sets the handles for the tabs.
     */
    public function __construct(Browser $browser, string $previousTabHandle, string $currentTabHandle)
    {
        $this->previousTabHandle = $previousTabHandle;
        $this->currentTabHandle = $currentTabHandle;

        parent::__construct($browser);
    }

    /**
     * Returns the previous tab handle.
     */
    public function getPreviousTab()
    {
        return $this->previousTabHandle;
    }

    /**
     * Returns the current tab handle.
     */
    public function getCurrentTab()
    {
        return $this->currentTabHandle;
    }

    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'tab.changed';
    }
}
