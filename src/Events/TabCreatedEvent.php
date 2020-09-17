<?php

namespace Anteris\Selenium\Client\Events;

class TabCreatedEvent extends AbstractTabEvent
{
    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'tab.created';
    }
}
