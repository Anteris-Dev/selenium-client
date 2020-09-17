<?php

namespace Anteris\Selenium\Client;

use Anteris\Selenium\Client\Events\BrowserStartedEvent;
use Anteris\Selenium\Client\Events\BrowserStoppedEvent;
use Anteris\Selenium\Client\Exceptions\InvalidScenarioException;
use Anteris\Selenium\Client\Events\ScenarioAddedEvent;
use Anteris\Selenium\Client\Events\ScenarioRunningEvent;
use Anteris\Selenium\Client\Events\ScenariosRunningEvent;
use Anteris\Selenium\Client\Events\TabChangedEvent;
use Anteris\Selenium\Client\Events\TabClosedEvent;
use Anteris\Selenium\Client\Events\TabCreatedEvent;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use League\Event\Emitter;
use ReflectionClass;

/**
 * This browser class gives you the flexability to interact with web browser tabs.
 */
class Browser
{
    /** @var RemoteWebDriver Our interface with the selenium driver. */
    protected RemoteWebDriver $driver;

    /** @var Emitter Handles the emittion of events. */
    protected Emitter $emitter;

    /** @var array An array of possible scenarios to be run. */
    protected array $scenarios = [];
    
    /** @var array This array keeps track of all our existing tabs. */
    protected array $tabs = [];

    /** @var array An array of window to scenario associations. */
    protected array $windowToScenarioAssociations = [];

    /**
     * The construct gets us all setup to begin working with selenium by establishing
     * a connection and listening for the interupt signal.
     */
    public function __construct(RemoteWebDriver $driver, Emitter $emitter)
    {
        /**
         * Setup our services.
         */
        $this->driver   = $driver;
        $this->emitter  = $emitter;

        /**
         * Send an event letting everyone know our browser is starting
         */
        $this->emitter->emit(new BrowserStartedEvent($this));

        /**
         * If we are running from the console and press Ctrl + C, the interrupt
         * will not close Selenium. These lines shutdown Selenium correctly if
         * we are interrupted.
         */
        pcntl_async_signals(true);

        pcntl_signal(SIGINT, function () {
            $this->quit();
            exit;
        });

        /**
         * Shutdown the client on exit
         */
        register_shutdown_function(function () {
            $this->quit();
        });
    }

    /**
     * Returns the selenium web driver.
     */
    public function getDriver(): RemoteWebDriver
    {
        return $this->driver;
    }

    /**
     * Returns the event emitter.
     */
    public function getEventManager(): Emitter
    {
        return $this->emitter;
    }

    /***************************************************************************
     * 
     * This section handles all the Scenario stuff.
     * 
     **************************************************************************/

     /**
      * Keeps a record of the scenario controlling a window.
      */
     public function associateWindowWithScenario(string $windowHandle, Scenario $scenario): void
     {
        $this->windowToScenarioAssociations[$windowHandle] = $scenario;
     }

     /**
      * Gets the scenario associated with a window handle.
      */
     public function getWindowsAssociatedScenario(string $windowHandle)
     {
         if (! isset($this->windowToScenarioAssociations[$windowHandle])) {
             return false;
         }

         return $this->windowToScenarioAssociations[$windowHandle];
     }

    /**
     * Adds a new scenario to the browser.
     */
    public function addScenario(string $class): void
    {
        // Add the scenario
        $reflectionClass = new ReflectionClass($class);

        if (!$reflectionClass->isSubclassOf(Scenario::class)) {
            throw new InvalidScenarioException(
                'Scenarios must extend "Anteris\Selenium\Client\Scenario"!'
            );
        }

        $this->scenarios[$reflectionClass->getShortName()] = $class;

        // Emit an event
        $this->emitter->emit(new ScenarioAddedEvent($this, $class));
    }

    /**
     * Adds multiple scenarios to the browser.
     */
    public function addScenarios(array $scenarios): void
    {
        foreach ($scenarios as $scenario) {
            $this->addScenario($scenario);
        }
    }

    /**
     * Retrieves the scenarios currently registered with the browser.
     */
    public function getScenarios(): array
    {
        return $this->scenarios;
    }

    /**
     * Runs the requested scenario.
     */
    public function runScenario(string $scenario): void
    {
        // We will run the scenario, but first we have to find it
        if (isset($this->scenarios[$scenario])) {
            $scenario = $this->scenarios[$scenario];
        } elseif (($key = array_search($scenario, $this->scenarios)) !== false) {
            $scenario = $this->scenarios[$key];
        } else {
            throw new InvalidScenarioException("Please register the $scenario scenario before attempting to run it.");
        }

        // Now run the scenario
        $scenario = new $scenario();
        $scenario->setBrowser($this);
        $scenario->run();

        // Dispatch an event
        $this->emitter->emit(new ScenarioRunningEvent($this, $scenario));
    }

    /**
     * Runs multiple scenarios.
     */
    public function runScenarios(?array $scenarios = null)
    {
        if ($scenarios === null) {
            $scenarios = $this->getScenarios();
        }

        foreach ($scenarios as $scenario) {
            $this->runScenario($scenario);
        }

        $this->emitter->emit(new ScenariosRunningEvent($this, $scenarios));
    }

    /***************************************************************************
     * 
     * This section handles all the Browser tab stuff.
     * 
     **************************************************************************/

    /**
     * Creates a new tab, sets the current context to that tab, and returns the
     * tab window ID.
     */
    public function createTab(): string
    {
        // Create the tab
        if (empty($this->tabs)) {
            $handle = $this->driver->getWindowHandle();
        } else {
            $this->driver->executeScript('window.open("", "_blank");');
            $handles = $this->driver->getWindowHandles();
            $handle = end($handles);
            $this->driver->switchTo()->window($handle);
        }

        $this->tabs[] = $handle;

        // Emit an event
        $this->emitter->emit(new TabCreatedEvent($this, $handle));

        // Return the handle
        return $handle;
    }

    /**
     * Closes all the tabs.
     */
    public function closeAll(): void
    {
        foreach ($this->tabs as $tab) {
            $this->closeTab($tab);
        }
    }

    /**
     * Closes an existing tab.
     */
    public function closeTab(int $windowHandle): void
    {
        if (! in_array($windowHandle, $this->tabs)) {
            return;
        }

        // Responsibly remove this window from our tabs array
        if (($key = array_search($windowHandle, $this->tabs)) !== false) {
            unset($this->tabs[$key]);
        }

        // Now remove the tab
        if (count($this->tabs) == 0) {
            $this->driver->quit();
        } else {
            $this->driver->switchTo()->window($windowHandle);
            $this->driver->close();
            $this->driver->switchTo()->window(end($this->tabs));
        }

        // Emit an event
        $this->emitter->emit(new TabClosedEvent($this, $windowHandle));
    }

    /**
     * Rotates amongst the currently open tabs.
     */
    public function rotateTab(): void
    {
        $previousHandle = $this->driver->getWindowHandle();

        // Nopity nope nope nope
        $hasPreviousHandle = in_array($previousHandle, $this->tabs);

        if (! $hasPreviousHandle && count($this->tabs) <= 0) {
            return;
        }

        if (! $hasPreviousHandle) {
            $handle = reset($this->tabs);
            $this->driver->switchTo()->window($handle);
            $this->emitter->emit(new TabChangedEvent($this, $previousHandle, $handle));
        }

        /**
         * If we reached the end of our tabs selection,
         * go back to the beginning.
         */
        if ($previousHandle == end($this->tabs)) {
            $handle = reset($this->tabs);
        } else {
            /**
             * Advance the pointer until we hit our current
             * tab, then switch to the one after that.
             */
            reset($this->tabs);
            while (current($this->tabs) !== $previousHandle) {
                next($this->tabs);
            }
    
            $handle = next($this->tabs);
        }

        $this->driver->switchTo()->window($handle);

        $this->emitter->emit(new TabChangedEvent($this, $previousHandle, $handle));

        return;
    }

    /***************************************************************************
     * 
     * Helpful little helpers
     * 
     **************************************************************************/

     /**
      * Quits the browser if currently running and emits an event.
      */
    public function quit()
    {
        if ($this->driver->getCommandExecutor()) {
            $this->driver->quit();
            $this->emitter->emit(new BrowserStoppedEvent($this));
        }
    }

    /**
     * This wait helper keeps the browser running.
     */
    public function wait(): void
    {
        while (true) {}
    }
}
