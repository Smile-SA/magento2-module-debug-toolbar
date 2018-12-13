# Installation

## Step 1 - Disable the native Magento Profiler

If you have already enabled the native **Magento profiler**, you must disable it.
Remove the `MAGE_PROFILER` environnement var.

## Step 2 - Install the module through composer

Execute the following command in your main project folder to add the module:

```bash
composer require --dev smile/module-debug-toolbar
```

Then, enable and install the module:

```bash
bin/magento module:enable Smile_DebugToolbar
bin/magento setup:upgrade
```

## Step 3 - Enable the toolbar

You have to enable the toolbar in the back-office to use it.

Look at the [Back-Office](backoffice.md) documentation.

[Back](../README.md)
