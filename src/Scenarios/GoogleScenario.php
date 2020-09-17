<?php

namespace Anteris\Selenium\Client\Scenarios;

use Anteris\Selenium\Client\Scenario;

/**
 * This is an example scenario that takes you to Google.com and searches
 * "this is awesome" in the search bar.
 *
 * @author Aidan Casey <aidan.casey@anteris.com>
 */
class GoogleScenario extends Scenario
{
    /**
     * We pass our URL to the parent scenario. This sets our base URL.
     */
    public function __construct()
    {
        parent::__construct('https://google.com');
    }

    /**
     * Here we perform any webpage actions we want to see take place. In this
     * example, we are searching "this is awesome"
     */
    public function run()
    {
        $this->getInNewTab('/');

        $this->element('[name=q]')->sendKeys('this is awesome')->submit();
    }
}
