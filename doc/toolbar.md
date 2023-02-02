# Toolbar

## Accessing the Toolbar

At the top of the page, you will see the **Smile ToolBar** button.
You can click on it to display the toolbar.

By default, it displays data about the 5 last executed requests.
The current page is not always the top most element of the list.
The top element is often an ajax request (e.g. magento_pagecache_block_esi).

If the button is **blue**, it means that there is no warning for the select request.
If the button is **orange**, it means that there is at least one warning.

For example:

![configuration](images/screenshot-zone-summary.png)

## Zones

The following zones are available:

- Generic
- Request
- Response
- Layout
- Mysql
- Cache
- Profiler
- Observer
- Preferences
- Summary

You can click on each zone label to see the concerned information.

If a zone is **orange**, it means that there is at least one warning in it.

For example:

![configuration](images/screenshot-zone-mysql.png)

In some zones, you can display additional information, by clicking on the links **Show xxx (xx rows)** at the top of the zone.

It will display a modal dialog with a sortable table.

In some tables, you can click on the row to display additional information.
For example, in the **mysql queries tables**, you can display the php trace: 

![configuration](images/screenshot-table-queries.png)

## Cache

If full page cache is enabled, cached requests will not be displayed in the toolbar.
Therefore, it is recommended to disable the full page cache when using the debug toolbar.

If the full page cache engine is properly configured (e.g. Varnish VCL file), it should be possible to disable the full page cache temporarily by enabling the option "Disable Cache" in the console of your browser.

[Back](../README.md)
