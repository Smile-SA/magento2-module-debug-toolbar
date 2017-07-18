
var smileToolbarList    = [];
var smileToolbarCount   = 0;
var smileToolbarCurrent = 0;
var smileToolbarOpen    = false;
var smileToolbarZone    = 'summary';

/**
 * Add a toolbar to the list of the available toolbars
 *
 * @param toolbarIdentifier
 */
function smileToolbarAdd(toolbarIdentifier)
{
    smileToolbarCount++;
    smileToolbarList[smileToolbarCount] = toolbarIdentifier;
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
        document.getElementById(smileToolbarList[smileToolbarCurrent]+'-toolbar').style.display = 'none';
        smileToolbarZoneReset();
    }

    smileToolbarCurrent = toolbarId;
    document.getElementById(smileToolbarList[smileToolbarCurrent]+'-toolbar').style.display = 'block';
    document.getElementById(smileToolbarList[smileToolbarCurrent]+'-name').innerHTML = smileToolbarCurrent + '/' + smileToolbarCount;

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
    document.getElementById(smileToolbarList[smileToolbarCurrent]+'-titles').style.display = (smileToolbarOpen ? 'block' : 'none');
    document.getElementById(smileToolbarList[smileToolbarCurrent]+'-zones').style.display  = (smileToolbarOpen ? 'block' : 'none');
}

/**
 * PROTECTED - reset the zones
 */
function smileToolbarZoneReset()
{
    if (smileToolbarZone) {
        document.getElementById(smileToolbarList[smileToolbarCurrent]+'-zone-'+smileToolbarZone).style.display = 'none';
        document.getElementById(smileToolbarList[smileToolbarCurrent]+'-title-'+smileToolbarZone).className = document.getElementById(smileToolbarList[smileToolbarCurrent]+'-title-'+smileToolbarZone).className.replace('st-selected', '');
    }
}

/**
 * PROTECTED - display a zone
 */
function smileToolbarZoneDisplay()
{
    if (smileToolbarZone) {
        document.getElementById(smileToolbarList[smileToolbarCurrent]+'-zone-'+smileToolbarZone).style.display = 'block';
        document.getElementById(smileToolbarList[smileToolbarCurrent]+'-zone-'+smileToolbarZone).style.display = 'block';
        document.getElementById(smileToolbarList[smileToolbarCurrent]+'-title-'+smileToolbarZone).className+= ' st-selected';
    }
}

/**
 * PROTECTED - display the navigator
 */
function smileToolbarNavigatorDisplay()
{
    var html = '';

    html+= '<ul>';

    for (var k=smileToolbarCount; k>0; k--) {
        var value = smileToolbarList[k].split('-');
        html+= '<li class="'+(k == smileToolbarCurrent ? 'st-selected' : '')+'">';
        html+= '<a onclick="smileToolbarSelect('+k+')">'
        html+= value[1]+' | '+value[4];
        html+= '</a>';
        html+= '</li>';
    }
    html+= '</ul>';

    document.getElementById(smileToolbarList[smileToolbarCurrent]+'-navigator').innerHTML = html;
}