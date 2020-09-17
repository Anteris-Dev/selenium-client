<?php

namespace Anteris\Selenium\Client\Events;

class TabClosedEvent extends AbstractTabEvent
{
    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'tab.closed';
    }
}
