<?php

namespace Anteris\Selenium\Client\Events;

use Anteris\Selenium\Client\Browser;
use Anteris\Selenium\Client\Scenario;

class ScenarioRunningEvent extends AbstractBrowserEvent
{
    /** @var Scenario The scenario that is currently running. */
    private Scenario $scenario;

    /**
     * Sets the scenario being run.
     */
    public function __construct(Browser $browser, Scenario $scenario)
    {
        $this->scenario = $scenario;

        parent::__construct($browser);
    }

    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'scenario.running';
    }

    /**
     * Returns the scenario being run.
     */
    public function getScenario()
    {
        return $this->scenario;
    }
}
