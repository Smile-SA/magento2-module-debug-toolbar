<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */
namespace Smile\DebugToolbar\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State as AppState;
use Magento\PageCache\Model\Config as PageCacheConfig;
use Smile\DebugToolbar\Block\Toolbar;

/**
 * Helper: Data
 *
 * @author    Laurent Minguet <dirtech@smile.fr>
 * @copyright 2019 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */
class Data extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var float[]
     */
    protected $timers = [];

    /**
     * @var string
     */
    protected $toolbarId;

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var int
     */
    protected $tableCount = 0;

    /**
     * @param Context $context
     * @param DirectoryList $directoryList
     * @param AppState $appState
     */
    public function __construct(Context $context, DirectoryList $directoryList, AppState $appState)
    {
        parent::__construct($context);

        $this->directoryList = $directoryList;
        $this->appState = $appState;
    }

    /**
     * Start a timer.
     *
     * @param string $code
     * @return $this
     */
    public function startTimer($code)
    {
        $this->timers[$code] = [
            'start' => microtime(true),
            'stop' => null,
            'delta' => null,
        ];

        return $this;
    }

    /**
     * Stop a timer.
     *
     * @param string $code
     * @return $this
     */
    public function stopTimer($code)
    {
        if (!array_key_exists($code, $this->timers)) {
            $this->startTimer($code);
        }

        if ($this->timers[$code]['stop'] === null) {
            $this->timers[$code]['stop'] = microtime(true);
            $this->timers[$code]['delta'] = $this->timers[$code]['stop'] - $this->timers[$code]['start'];
        }

        return $this;
    }

    /**
     * Get a timer.
     *
     * @param string $code
     * @return float
     */
    public function getTimer($code)
    {
        $this->stopTimer($code);

        return $this->timers[$code]['delta'];
    }

    /**
     * Get the timers.
     *
     * @return array
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Set a value.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setValue($key, $value)
    {
        $this->values[$key] = $value;

        return $this;
    }

    /**
     * Get a value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @throws \Exception
     */
    public function getValue($key, $default = null)
    {
        if (!array_key_exists($key, $this->values)) {
            return $default;
        }

        return $this->values[$key];
    }

    /**
     * Init the toolbar id.
     *
     * @param string $actionName
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PMD.StaticAccess)
     */
    public function initToolbarId($actionName)
    {
        if ($this->toolbarId !== null) {
            throw new \Exception('The toolbar id has already been set');
        }

        $date = \DateTime::createFromFormat('U.u', microtime(true));

        $values = [
            'st',
            $date->format('Ymd_His'),
            $date->format('u'),
            uniqid(),
            $this->appState->getAreaCode(),
            $actionName,
        ];

        $this->toolbarId = implode('-', $values);

        return $this->toolbarId;
    }

    /**
     * Get toolbar id.
     *
     * @return string
     * @throws \Exception
     */
    public function getToolbarId()
    {
        if ($this->toolbarId === null) {
            throw new \Exception('The toolbar id has not been set');
        }

        return $this->toolbarId;
    }

    /**
     * Get a new table id.
     *
     * @return string
     */
    public function getNewTableId()
    {
        $this->tableCount++;

        return $this->toolbarId . '_table_' . $this->tableCount;
    }

    /**
     * Get the toolbar storage folder.
     *
     * @return string
     */
    public function getToolbarFolder()
    {
        $folder = $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'smile_toolbar' . DIRECTORY_SEPARATOR;

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        return $folder;
    }

    /**
     * Save the current toolbar.
     *
     * @param Toolbar $toolbarBlock
     */
    public function saveToolbar(Toolbar $toolbarBlock)
    {
        $filename = $this->getToolbarFolder() . $toolbarBlock->getToolbarId() . '.html';

        file_put_contents($filename, $toolbarBlock->toHtml());
    }

    /**
     * Clean the old toolbars.
     *
     * @param int $nbToKeep
     */
    public function cleanOldToolbars($nbToKeep)
    {
        $list = $this->getListToolbars();

        if (count($list) > $nbToKeep) {
            $toDelete = array_slice($list, 0, count($list) - $nbToKeep);

            $folder = $this->getToolbarFolder();
            foreach ($toDelete as $file) {
                if (is_file($folder . $file)) {
                    unlink($folder . $file);
                }
            }
        }
    }

    /**
     * Get the list of all the stored toolbars.
     *
     * @return string[]
     */
    public function getListToolbars()
    {
        $folder = $this->getToolbarFolder();

        $list = array_diff(scandir($folder), ['.', '..']);

        foreach ($list as $key => $value) {
            if (!is_file($folder . $value) || is_dir($folder . $value)) {
                unset($list[$key]);
            }
        }

        sort($list);

        return $list;
    }

    /**
     * Get the content of all the stored toolbars.
     *
     * @return string[]
     */
    public function getContentToolbars()
    {
        $list = $this->getListToolbars();

        $contents = [];

        $folder = $this->getToolbarFolder();
        foreach ($list as $filename) {
            $key = explode('.', $filename)[0];
            $contents[$key] = file_get_contents($folder . $filename);
        }

        return $contents;
    }

    /**
     * Get the Full Page Cache mode.
     *
     * @return string
     */
    public function getFullPageCacheMode()
    {
        $key = 'system/full_page_cache/caching_application';

        return $this->scopeConfig->getValue($key) == PageCacheConfig::VARNISH ? 'varnish' : 'build-in';
    }
}
