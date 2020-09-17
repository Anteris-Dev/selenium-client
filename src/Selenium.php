<?php

namespace Anteris\Selenium\Browser;

use Exception;
use Symfony\Component\Process\Process;

class Selenium
{
    /** @var Browser Contains our "web browser". */
    public Browser $browser;

    /** @var array Contains an array of all the scenarios we will run. */
    protected array $scenarios = [];

    /**
     * Registers a new scenario / set of scenarios.
     */
    public function register($scenarios)
    {
        if (! is_array($scenarios)) {
            $scenarios = [$scenarios];
        }

        foreach ($scenarios as $scenario) {
            if (! ((new $scenario) instanceof Scenario)) {
                throw new Exception('Registered scenarios must extend "Anteris\Selenium\Browser\Scenario"!');
            }

            $this->scenarios[] = $scenario;
        }
    }

    public function execute()
    {
        // $seleniumApp = new Console;

        // $input  = new ArrayInput([]);
        // $output = new NullOutput();

        // $seleniumApp->find('serve')->run($input, $output);
        // $process = new Process([__DIR__ . '/../vendor/bin/selenium', 'serve']);
        // $process->start();

        $this->browser = new Browser();

        foreach ($this->scenarios as $scenario) {
            $class = new $scenario();
            $class->setBrowser($this->browser);
            $class->run();
        }
    }
}
