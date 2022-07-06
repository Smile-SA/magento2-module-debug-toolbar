# Installation

**WARNING**: do not install this module on production environments.
It is designed to be used on development environments only.
If the module is installed but left disabled, it will still have a small impact on performance.

## Step 1 - Disable the native Magento Profiler

If you have already enabled the native **Magento profiler**, you must disable it.
Remove the `MAGE_PROFILER` environment var.

## Step 2 - Install the module through composer

Execute the following command in your main project folder to add the module on your development environment:

```bash
composer require --dev smile/module-debug-toolbar
```

The `--dev` option is important, it will ensure that the module is installed only on development environments.

Then, enable the module:

```bash
bin/magento module:enable Smile_DebugToolbar
bin/magento setup:upgrade
```

## Step 3 - Enable the toolbar

Execute the following command:

```
bin/magento config:set smile_debugtoolbar/configuration/enabled 1
```

# Uninstallation

## Step 1 - Remove the module

Use the Magento command line to uninstall the module:

```
bin/magento module:uninstall Smile_DebugToolbar
```

## Step 2 - Clean up the configuration

In app/etc/env.php, remove any reference to this module (Smile_DebugToolbar).

[Back](../README.md)
