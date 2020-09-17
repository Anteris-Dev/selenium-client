<?php

namespace Anteris\Selenium\Client;

use Anteris\Selenium\Client\Browser;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class Scenario
{
    protected Browser $browser;
    protected RemoteWebDriver $driver;
    protected string $host;

    /**
     * Sets the base url for our scenario.
     */
    public function __construct(string $host = '')
    {
        $this->host = rtrim($host, '/');
    }

    /**
     * Sets the browser used by this class.
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
        $this->driver  = $browser->getDriver();
    }

    /**
     * Gets the specified url in the current window.
     */
    public function get(string $url = '/')
    {
        $result = $this->driver->get($this->getUrl($url));
        $this->browser->associateWindowWithScenario($result->getWindowHandle(), $this);
        return $result;
    }

    /**
     * Gets the specified url in a new tab.
     */
    public function getInNewTab(string $url = '/')
    {
        $this->browser->createTab();
        return $this->get($url);
    }

    /**
     * Returns an element retrieved by its CSS selector.
     */
    public function element(string $selector): RemoteWebElement
    {
        return $this->driver->findElement(WebDriverBy::cssSelector($selector));
    }

    /**
     * Returns multiple elements retrieved by their CSS selector.
     * 
     * @param string $selector
     *
     * @return RemoteWebElement[]
     */
    public function elements(string $selector): array
    {
        return $this->driver->findElements(WebDriverBy::cssSelector($selector));
    }

    /**
     * Retrieves a link by its text.
     */
    public function linkByText(string $text): RemoteWebElement
    {
        return $this->driver->findElement(WebDriverBy::linkText($text));
    }

    /**
     * Clicks the specified element.
     */
    public function click(string $selector): RemoteWebElement
    {
        return $this->element($selector)->click();
    }

    /**
     * Submits the element.
     */
    public function submit(string $selector): RemoteWebElement
    {
        return $this->element($selector)->submit();
    }

    /**
     * Selects the element.
     */
    public function select(string $selector, int $index)
    {
        (new WebDriverSelect($this->element($selector)))->selectByIndex($index);
    }

    /**
     * Sends a filepath to the input.
     */
    public function upload(string $selector, string $path)
    {
        $this->element($selector)->sendKeys($path);
    }

    /**
     * Scrolls to the specified position.
     */
    public function scrollTo(string $selector)
    {
        $actions = new WebDriverActions($this->driver);

        $actions->moveToElement($this->element($selector));

        $actions->perform();
    }

    /**
     * Sleeps for the specified number of seconds.
     */
    public function timeout(int $seconds): void
    {
        sleep($seconds);
    }

    /**
     * Returns the browser.
     */
    public function browser()
    {
        return $this->browser;
    }

    /**
     * Returns the driver.
     */
    public function driver(): WebDriver
    {
        return $this->driver();
    }

    /**
     * Builds the url.
     */
    protected function getUrl(string $url): string
    {
        $url = ltrim($url, '/');

        return "{$this->host}/{$url}";
    }
}
