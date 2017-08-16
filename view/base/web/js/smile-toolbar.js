
var smileToolbarList    = [];
var smileToolbarCount   = 0;
var smileToolbarCurrent = 0;
var smileToolbarOpen    = false;
var smileToolbarZone    = 'summary';

/**
 * Add a toolbar to the list of the available toolbars
 *
 * @param toolbarIdentifier
 * @param hasWarning
 */
function smileToolbarAdd(toolbarIdentifier, hasWarning)
{
    smileToolbarCount++;

    smileToolbarList[smileToolbarCount] = {
        id:      toolbarIdentifier,
        warning: hasWarning
    };
}

/**
 * Init the display of the last toolbar
 */
function smileToolbarInit()
{
    smileToolbarSelect(smileToolbarCount);
}

/**
 * Go the the previous toolbar
 */
function smileToolbarPrevious()
{
    if (smileToolbarCurrent > 1) {
        smileToolbarSelect(smileToolbarCurrent - 1);
    }
}

/**
 * Go to the next toolbar
 */
function smileToolbarNext()
{
    if (smileToolbarCurrent < smileToolbarCount) {
        smileToolbarSelect(smileToolbarCurrent + 1);
    }
}

/**
 * Select a toolbar
 *
 * @param toolbarId
 */
function smileToolbarSelect(toolbarId)
{
    if (smileToolbarCurrent) {
        document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-toolbar').style.display = 'none';
        smileToolbarZoneReset();
    }

    smileToolbarCurrent = toolbarId;
    document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-toolbar').style.display = 'block';
    document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-name').innerHTML = smileToolbarCurrent + '/' + smileToolbarCount;

    smileToolbarMainInit();
    smileToolbarZoneDisplay();
    smileToolbarNavigatorDisplay();
}

/**
 * Toggle the display of the toolbar
 */
function smileToolbarMainToggle()
{
    smileToolbarOpen = !smileToolbarOpen;
    smileToolbarMainInit();
}

/**
 * Display a zone
 *
 * @param zoneId
 */
function smileToolbarZoneSelect(zoneId)
{
    smileToolbarZoneReset();
    smileToolbarZone = zoneId;
    smileToolbarZoneDisplay();
}

/**
 * Display a table
 *
 * @param values
 * @param columns
 */
function smileToolbarTableDisplay(values, columns, additional)
{
    document.getElementById('st-table-display').style.display = 'block';

    smileTableOpen(values, columns, additional, 'st-table-content');
}

/**
 * Hide a table
 */
function smileToolbarTableHide()
{
    document.getElementById('st-table-display').style.display = 'none';

    smileTableClose();
}

/**
 * PROTECTED - init the toolbar
 */
function smileToolbarMainInit()
{
    document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-titles').style.display = (smileToolbarOpen ? 'block' : 'none');
    document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-zones').style.display  = (smileToolbarOpen ? 'block' : 'none');
}

/**
 * PROTECTED - reset the zones
 */
function smileToolbarZoneReset()
{
    if (smileToolbarZone) {
        document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-zone-'+smileToolbarZone).style.display = 'none';
        document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-title-'+smileToolbarZone).className = document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-title-'+smileToolbarZone).className.replace('st-selected', '');
    }
}

/**
 * PROTECTED - display a zone
 */
function smileToolbarZoneDisplay()
{
    if (smileToolbarZone) {
        document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-zone-'+smileToolbarZone).style.display = 'block';
        document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-zone-'+smileToolbarZone).style.display = 'block';
        document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-title-'+smileToolbarZone).className+= ' st-selected';
    }
}

/**
 * PROTECTED - display the navigator
 */
function smileToolbarNavigatorDisplay()
{
    var html = '';

    html+= '<h1>Last '+smileToolbarCount+' executions</h1>';

    html+= "<table>\n";
    html+= "<tr><th>Date</th><th>Area</th><th>Action</th></tr>\n";

    for (var k=smileToolbarCount; k>0; k--) {
        var value = smileToolbarList[k].id.split('-');
        var htmlClass = (k === smileToolbarCurrent ? 'st-selected' : '');
        var date = value[1];

        date = date.replace(/^([0-9]{4})([0-9]{2})([0-9]{2})_([0-9]{2})([0-9]{2})([0-9]{2})$/, '$1-$2-$3 $4:$5:$6');

        if (smileToolbarList[k].warning) {
            htmlClass+= ' value-warning';
        }

        html+= '<tr onclick="smileToolbarSelect('+k+');">';
        html+= '<td class="'+htmlClass+'">'+date+'</td>';
        html+= '<td class="'+htmlClass+'">'+value[4]+'</td>';
        html+= '<td class="'+htmlClass+'">'+value[5]+'</td>';
        html+= "</tr>\n";
    }
    html+= '</table>';

    document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-navigator').innerHTML = html;
}