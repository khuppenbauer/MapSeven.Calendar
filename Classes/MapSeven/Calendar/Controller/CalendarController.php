<?php
namespace MapSeven\Calendar\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "MapSeven.Calendar".     *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Google_Client;
use Google_Auth_AssertionCredentials;
use Google_Service_Calendar;

/**
 * Controller that displays the assets with the given tag(s)
 */
class CalendarController extends ActionController
{

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
     * @return void
     */
    public function listAction()
    {
        $node = $this->request->getInternalArgument('__node');
        if ($node === null) {
            return;
        }
        $eventsArray = array();
        $properties = $node->getProperties();
        $optParams['singleEvents'] = true;
        $optParams['orderBy'] = $properties['orderBy'];
        $optParams['timeMin'] = date("c", time());
        if (!empty($properties['maxDays'])) {
            $timestamp = strtotime("+" . $properties['maxDays'] . " day");
            $optParams['timeMax'] = date("c", $timestamp);
        }
        if (!empty($properties['maxResults'])) {
            $optParams['maxResults'] = $properties['maxResults'];
        }
        $client = $this->createClient();
        $service = new Google_Service_Calendar($client);
        if (!empty($properties['googleCalendars'])) {
            foreach ($properties['googleCalendars'] as $googleCalendar) {
                $events = $service->events->listEvents($googleCalendar, $optParams);
                foreach ($events as $event) {
                    if (!empty($event['start']['dateTime'])) {
                        $timestamp = strtotime($event['start']['dateTime']);
                        $eventsArray[$timestamp][$event['id']]['dateTime'] = $event['start']['dateTime'];
                    } elseif (!empty($event['start']['date'])) {
                        $event['end']['date'] = date('Y-m-d', strtotime($event['end']['date'] . ' -1 day'));
                        $timestamp = strtotime($event['start']['date']);
                        $eventsArray[$timestamp][$event['id']]['start'] = $event['start']['date'];
                        if ($event['start']['date'] !== $event['end']['date']) {
                            $eventsArray[$timestamp][$event['id']]['end'] = $event['end']['date'];
                        }
                    }
                    if (isset($timestamp)) {
                        $eventsArray[$timestamp][$event['id']]['summary'] = $event['summary'];
                    }
                }
            }
        }
        if (!empty($eventsArray)) {
            ksort($eventsArray);
            $eventsArray= call_user_func_array('array_merge', $eventsArray);
            if (!empty($properties['maxResults'])) {
                array_splice($eventsArray, $properties['maxResults']);
            }
        }
        $this->view->assign('events', $eventsArray);
    }

    /**
     * @return Google_Client
     */
    protected function createClient()
    {
        $keyFile = $this->settings['keyFilePath'] . $this->settings['keyFileName'];
        $client = new Google_Client();
        $client->setApplicationName("Client_Library_Examples");
        $key = file_get_contents($keyFile);
        $credentials = new Google_Auth_AssertionCredentials(
            $this->settings['emailAddress'],
            array(Google_Service_Calendar::CALENDAR_READONLY),
            $key
        );
        $client->setAssertionCredentials($credentials);
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($credentials);
        }
        return $client;
    }
}
