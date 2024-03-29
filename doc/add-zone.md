# How to Add Zones

It is possible add new zones (i.e. sections) to the toolbar.
To do this, you must implement a new block associated to a template file.

This new block must:

- extend the class `\Smile\DebugToolbar\Block\Zone\AbstractZone`
- implement the method `getTitle`
- implement the method `getCode`

For example:

```php
<?php

declare(strict_types=1);

namespace MyNameSpace\MyModule\Block\Zone;

use Smile\DebugToolbar\Block\Zone\AbstractZone;

class MyZone extends AbstractZone
{
    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return 'myzone';
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return 'My Zone';
    }
}
```

It must be associated to the template file `view/base/templates/zone/myzone.phtml` of your module:

```php
<?php

/** @var \MyNameSpace\MyModule\Block\Zone\MyZone $block */

$sections = [
    'Some Values' => [
        'Current Date' => $block->formatValue(date('Y-m-d H:i:s'), [], 'datetime'),
        'Memory Used' => $block->formatValue((int) memory_get_peak_usage(true), ['gt' => 128*1024*1024], 'size'),
    ],
];

$block->addToSummary('Server', 'PHP Memory Used', $sections['Some Values']['Memory Used']);

echo $block->displaySections($sections);
```

You can:

- Format values with the `formatValue` method.
- Display sections with the `displaySections` method.
- Add values to the summary zone with the `addToSummary` method.

Then, you can add this new zone to the toolbar by adding an observer on the event `smile_debug_toolbar_set_zones`.

The following objects will be available in the event:

- `zones`: contains the list of the current zones.
- `summary_block`: contains the summary zone.

For example:

```php
<?php

declare(strict_types=1);

namespace MyNameSpace\MyModule\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MyNameSpace\MyModule\Block\Zone\MyZoneFactory;

class AddZone implements ObserverInterface
{
    public function __construct(private MyZoneFactory $zoneFactory)
    {
    }

    /**
     * @inheritdoc
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
