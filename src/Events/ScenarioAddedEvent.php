<?php

namespace Anteris\Selenium\Client\Events;

use Anteris\Selenium\Client\Browser;

class ScenarioAddedEvent extends AbstractBrowserEvent
{
    /** @var string Class name of the scenario being added. */
    private string $scenario;

    /**
     * Sets the scenario being added.
     */
    public function __construct(Browser $browser, string $scenario)
    {
        $this->scenario = $scenario;

        parent::__construct($browser);
    }

    /**
     * Defines the name of the event.
     */
    public function getName()
    {
        return 'scenario.added';
    }

    /**
     * Returns the scenario being added.
     */
    public function getScenario()
    {
        return $this->scenario;
    }
}
