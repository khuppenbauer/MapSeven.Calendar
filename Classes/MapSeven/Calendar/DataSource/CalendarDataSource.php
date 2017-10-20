<?php
namespace MapSeven\Calendar\DataSource;

use Neos\Flow\Annotations as Flow;
use TYPO3\Neos\Service\DataSource\AbstractDataSource;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use Google_Client;
use Google_Auth_AssertionCredentials;
use Google_Service_Calendar;

/**
 * Class CalendarDataSource
 *
 * @package MapSeven\Calendar\DataSource
 */
class CalendarDataSource extends AbstractDataSource
{

    /**
     * @var string
     */
    protected static $identifier = 'mapseven-calendar';

    /**
     * @var string
     */
    protected $settings;


    /**
     * @param array $settings
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Get data
     *
     * @param NodeInterface $node The node that is currently edited (optional)
     * @param array $arguments Additional arguments (key / value)
     * @return array JSON serializable data
     */
    public function getData(NodeInterface $node = null, array $arguments)
    {
        $keyFileLocation = $this->settings['keyFilePath'] . $this->settings['keyFileName'];
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $key = file_get_contents($keyFileLocation);
        $cred = new Google_Auth_AssertionCredentials(
                $this->settings['emailAddress'],
                array(Google_Service_Calendar::CALENDAR_READONLY),
                $key
        );
        $client->setAssertionCredentials($cred);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }
        $service = new Google_Service_Calendar($client);
        $calendarList  = $service->calendarList->listCalendarList();
        $calendarItems = $calendarList->getItems();
        $calendarArray = array();
        foreach ($calendarItems as $calendar) {
            $id = $calendar->getId();
            $label = $calendar->getSummary();
            $calendarArray[$id]['label'] = $label;
            $calendarArray[$id]['icon'] = 'icon-calendar';
        }
        return $calendarArray;
    }
}
