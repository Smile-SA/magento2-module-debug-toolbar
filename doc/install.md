# Installation

**WARNING**: do not install this module on production environments.
It is designed to be used on development environments only.
If the module is installed but left disabled, it will still have a small impact on performance.

## Step 1 - Disable the native Magento Profiler

If you have already enabled the native **Magento profiler**, you must disable it.
Remove the `MAGE_PROFILER` environment variable if it is defined.

## Step 2 - Install the module with Composer

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

Run the following command:

```
bin/magento config:set smile_debugtoolbar/configuration/enabled 1
```

# Uninstallation

Unfortunately, the module cannot be automatically uninstalled, because Magento doesn't support uninstallation of dev packages out of the box.

To uninstall the module, follow these steps:

1. In app/etc/env.php, remove the key `db.connection.default.profiler` from the array if it is defined.
2. Run the following commands:
    ```
    bin/magento module:disable Smile_DebugToolbar
    composer remove --dev smile/module-debug-toolbar
    bin/magento setup:upgrade
    ```
3. [Optional] Remove the directory "var/smile_toolbar".

[Back](../README.md)
