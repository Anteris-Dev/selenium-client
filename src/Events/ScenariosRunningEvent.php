<?php

namespace Anteris\Selenium\Client\Events;

use Anteris\Selenium\Client\Browser;

class ScenariosRunningEvent extends AbstractBrowserEvent
{
    /** @var array The scenarios that are currently running. */
    private array $scenarios;

    /**
     * Sets the scenarios being run.
     */
    public function __construct(Browser $browser, array $scenarios)
    {
        $this->scenarios = $scenarios;

        parent::__construct($browser);
    }

    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'scenarios.running';
    }

    /**
     * Returns the scenario being run.
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }
}
