<?php

namespace Anteris\Selenium\Client\Events;

class BrowserStoppedEvent extends AbstractBrowserEvent
{
    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'browser.stopped';
    }
}
