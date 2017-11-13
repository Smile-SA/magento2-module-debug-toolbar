# Smile Debug Toolbar for Magento 2

## Description

This module adds a Debug Toolbar

Capi : https://capi.smile.fr/PHP/Magento2/Smile-DebugToolbar

Use it only on dev environnement.

## Install

### Step 1

You have to disable the native Magento profiler, by removing the `MAGE_PROFILER` environnement var.

**note**: if you use the Smile Magento 2 Skeleton, you have just to set the value of `magento_profiler` to `off` on the `provisioning/inventory/group_vars/lxc` file. Then launch the provisioning script.

### Step 2

Execute the following command on your main project folder to add the module:

> composer require --dev smile/module-debug-toolbar --ignore-platform-reqs

Then, do a `setup upgrade` to install the module.

### Step 3

You have to enable the toolbar in the **Store Configuration** screen of the Back-Office, in the **Smile > Smile DebugToolbar** section. 


## Magento versions compatibility

 * CE: 2.2.x
 * EE: 2.2.x

## Contact

Laurent MINGUET <lamin@smile.fr>
