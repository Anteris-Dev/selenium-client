<?php

namespace Anteris\Selenium\Client\Events;

class BrowserStartedEvent extends AbstractBrowserEvent
{
    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'browser.started';
    }
}
