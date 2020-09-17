<?php

namespace Anteris\Selenium\Client\Scenarios;

use Anteris\Selenium\Client\Scenario;

class BingScenario extends Scenario
{
    public function __construct()
    {
        parent::__construct('https://bing.com');
    }

    /**
     * Here we perform any webpage actions we want to see take place. In this
     * example, we are going to Bing.com and searching "this is awesome"
     */
    public function run()
    {
        $this->getInNewTab();

        $this->element('[name=q]')->sendKeys('this is awesome')->submit();
    }
}
