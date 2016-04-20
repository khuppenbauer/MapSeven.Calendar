# MapSeven.Calendar
Simple Calendar plugin for Neos CMS with [FullCalendar](http://fullcalendar.io) as frontend and [Google Calendar](https://calendar.google.com) as backend

## Installation
`composer require mapseven/calendar`

## Features
This Package contains:

* A Neos CMS Module to manage your Google API Configuration (API Key, E-Mail Address, Key-File)
* NodeTypes for Calendar View (FullCalendar) and Calender List

### Using the Google Calendar API
This Package communicates with the Google Calendar API through the [Google API Client Library](https://packagist.org/packages/google/apiclient).
To use the Google Calendar API you have to activate it in the [Google Console](https://console.developers.google.com) and create your API Key and Service Account. After Downloading the generated Key-File (choose P12 as Key Type) you can upload it in the Neos CMS Module.
Find more Informations about working with the [API Client Library for PHP](https://developers.google.com/api-client-library/php/auth/service-accounts)

![Calendar Module](/Module_Calendar.png "Neos CMS Module to manage your Google API Configuration")

### Share the Google Calendar
To use a Google Calendar for your Event Listing you have to make it public and share it with your Service Account, but you can restrict the Permissions to Read Access.

### Calendar NodeType
This Package comes with two NodeTypes

* Calendar List: Choose one or more of your public Calendars, add more options for displaying the Events and adjust the template to your needs through the [Views.yaml](http://flowframework.readthedocs.org/en/stable/TheDefinitiveGuide/PartIII/ModelViewController.html#configuring-views-through-views-yaml)
* Calendar View: Choose one or more of your public Calendars, the Rendering is done through FullCalendar

![Calendar List NodeType](/NodeType_Calendar_List.png "Neos CMS NodeType Calendar List")

## License
MapSeven.Calendar is licensed under the [MIT Licence](LICENSE)

