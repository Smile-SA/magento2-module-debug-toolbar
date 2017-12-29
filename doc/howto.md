# Smile Debug Toolbar for Magento 2

[Back](README.md)

## How To

### Add a new zone

A zone in a specific block associated to a template file.
You can add a new zone in the toolbar.

Your new block must :

* extend the class `\Smile\DebugToolbar\Block\Zone\AbstractZone`.
* implement the method `getTitle` (gives the name of the zone).
* implement the method `getCode` (gives the code of the zone).

```php
<?php
namespace MyNameSpace\MyModule\Block\Zone;

use Smile\DebugToolbar\Block\Zone\AbstractZone;

class MyZone extends AbstractZone
{
    /**
     * Get the Code
     *
     * @return string
     */
    public function getCode()
    {
        return 'myzone';
    }

    /**
     * Get the Title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'My Zone';
    }
}
```

It must be associated to the template file `./view/base/templates/zone/myzone.phtml` of your module:

```php
<?php
/** @var \MyNameSpace\MyModule\Block\Zone\MyZone $block */

$sections = [
    'Some Values' => [
        'Current Date' => $block->formatValue(date('Y-m-d H:i:s'), [], 'datetime'),
        'Memory Used'  => $block->formatValue((int) memory_get_peak_usage(true), ['gt' => 128*1024*1024], 'size'),
    ],
];

$block->addToSummary('Server', 'PHP Memory Used', $sections['Some Values']['Memory Used']);

echo $block->displaySections($sections);

```
You can:

* format values, using the `formatValue` method. You can specify rules to generate automatic warnings.
* display sections, using the `displaySections` method.
* add values to the summary zone, using the `addToSummary` method.


Then, you can add this new zone, by adding an observer on the event `smile_debug_toolbar_set_zones`.

The following objects will be available in the event:

* `zones`: contains the list of all the current zones.
* `summary_block`: contains the summary zone.

You can use them as follow:

```php
<?php
namespace MyNameSpace\MyModule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use MyNameSpace\MyModule\Block\Zone\MyZoneFactory;

class AddZone implements ObserverInterface
{
    /**
     * @var MyZoneFactory
     */
    protected $zoneFactory;

    /**
     * AddZone constructor.
     *
     * @param MyZoneFactory $zoneFactory
     */
    public function __construct(
        MyZoneFactory      $zoneFactory
    ) {
        $this->zoneFactory = $zoneFactory;
    }

    /**
     * Execute the observer
     *
     * @param Observer $observer Magento Observer Object
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $zone = $this->zoneFactory->create();
        $zone->setSummaryBlock($observer->getEvent()->getData('summary_block'));

        $list = $observer->getEvent()->getData('zones')->getData('list');
        $list[] = $zone;
        $observer->getEvent()->getData('zones')->setData('list', $list);
    }
}
```

[Back](README.md)
