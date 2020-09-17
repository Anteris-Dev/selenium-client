<?php

namespace Anteris\Selenium\Client\Factories;

use League\Event\Emitter;

class EventManagerFactory
{
    /**
     * Creates a new event emitter.
     */
    public static function create(): Emitter
    {
        return new Emitter;
    }
}
