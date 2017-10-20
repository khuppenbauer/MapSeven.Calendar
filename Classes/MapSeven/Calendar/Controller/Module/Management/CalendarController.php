<?php
namespace MapSeven\Calendar\Controller\Module\Management;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "MapSeven.Calendar".     *
 *                                                                        *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\Configuration\Source\YamlSource;
use Neos\Flow\Package\PackageManagerInterface;
use Neos\Utility\Arrays;
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Utility\Files;
use TYPO3\Neos\Controller\Module\AbstractModuleController;

/**
 * Calendar Management Module Controller
 *
 * @package MapSeven\Calendar\Controller\Module\Management
 */
class CalendarController extends AbstractModuleController
{

    /**
     * @Flow\Inject
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @Flow\Inject
     * @var YamlSource
     */
    protected $configurationSource;

    /**
     * @Flow\Inject
     * @var PackageManagerInterface
     */
    protected $packageManager;

    /**
     * The settings parsed from Settings.yaml
     *
     * @var array
     */
    protected $settings;


    /**
     * Inject settings
     *
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * A edit view for the global calendar settings
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->assignMultiple(array(
            'calendarSettings' => $this->settings
        ));
    }

    /**
     * Update global Calendar settings
     *
     * @param array $calendar
     * @return void
     */
    public function updateAction(array $calendar)
    {
        if (!empty($calendar['keyFile'])) {
            $calendar['keyFileName'] = $calendar['keyFile']['name'];
            $finalTargetPathAndFilename = $this->settings['keyFilePath'] . $calendar['keyFile']['name'];
            Files::createDirectoryRecursively($this->settings['keyFilePath']);
            move_uploaded_file($calendar['keyFile']['tmp_name'], $finalTargetPathAndFilename);
            unset($calendar['keyFile']);
        }

        $settings = $this->configurationSource->load(FLOW_PATH_CONFIGURATION . ConfigurationManager::CONFIGURATION_TYPE_SETTINGS);
        $settings = Arrays::setValueByPath($settings, 'MapSeven.Calendar', $calendar);
        $this->configurationSource->save(FLOW_PATH_CONFIGURATION . ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, $settings);
        $this->configurationManager->flushConfigurationCache();
        $this->redirect('index');
    }
}
