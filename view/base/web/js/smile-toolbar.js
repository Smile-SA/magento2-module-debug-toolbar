/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 *
 *
 * @author    Laurent MINGUET <dirtech@smile.fr>
 * @copyright 2018 Smile
 * @license   Eclipse Public License 2.0 (EPL-2.0)
 */

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
    setTimeout('smileToolbarHighlight();', 500);
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
function smileToolbarTableDisplay(title, values, columns, additional)
{
    smileToolbarModalShow();

    document.getElementById('st-modal-display').onclick = smileToolbarTableHide;
    document.getElementById('st-modal-close').onclick   = smileToolbarTableHide;

    smileTableOpen(title, values, columns, additional, 'st-modal-title', 'st-modal-content');
}

/**
 * Hide the Table
 */
function smileToolbarTableHide()
{
    smileToolbarModalHide();

    smileTableClose();
}

/**
 * Show the Modal
 */
function smileToolbarModalShow()
{
    document.getElementById('st-modal-display').style.display = 'block';

    document.getElementById('st-modal-display').onclick = smileToolbarModalHide;
    document.getElementById('st-modal-close').onclick   = smileToolbarModalHide;

    document.getElementById('st-modal-title').innerHTML   = "My Modal Title";
    document.getElementById('st-modal-content').innerHTML = "My Modal Content";
}

/**
 * Hide the modal
 */
function smileToolbarModalHide()
{
    document.getElementById('st-modal-display').style.display = 'none';

    document.getElementById('st-modal-display').onclick = smileToolbarModalHide;
    document.getElementById('st-modal-close').onclick   = smileToolbarModalHide;

    document.getElementById('st-modal-title').innerHTML   = "My Modal Title";
    document.getElementById('st-modal-content').innerHTML = "My Modal Content";
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

    html+= '<div class="st-title">';
    html+= '<h1>Last '+smileToolbarCount+' executions</h1>';
    html+= '</div>';
    html+= '<div class="st-content">';
    html+= "<table>\n";
    html+= '<col style="width: 150px" />';
    html+= '<col style="width: 100px" />';
    html+= '<col />';
    html+= "<thead><tr><th>Date</th><th>Area</th><th>Action</th></tr></thead>\n";
    html+= '<tbody>';

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
    html+= '</tbody>';
    html+= '</table>';
    html+= '</div>';

    document.getElementById(smileToolbarList[smileToolbarCurrent].id+'-navigator').innerHTML = html;
}

function smileToolbarTreeGrid(node, forceClose)
{
    var status = (node.innerHTML === '-');
    var expand = !status;

    if (forceClose) {
        expand = false;
    }

    if (!expand && !status) {
        return false;
    }

    node.innerHTML = (expand ? '-' : '+');

    var spans = document.getElementsByClassName(node.id.replace('-span', ''));

    for (var k=0; k<spans.length; k++) {
        spans[k].style.display = (expand ? '': 'none');
        if (!expand) {
            var span=document.getElementById(spans[k].id+'-span');
            if (span) {
                smileToolbarTreeGrid(span, true);
            }
        }
    }

    return true;
}

/**
 * HighLight the codes
 */
function smileToolbarHighlight()
{
    var blocks = document.querySelectorAll('pre code');
    [].forEach.call(blocks, hljs.highlightBlock);
}