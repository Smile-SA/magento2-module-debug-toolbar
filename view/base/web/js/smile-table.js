/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future.
 */

var smileTableValues        = [];
var smileTableColumns       = [];
var smileTableAdditional    = null;
var smileTableContainer     = null;
var smileTableCurrentColumn = null;
var smileTableCurrentOrder  = null;

/**
 * Open a table
 *
 * @param title
 * @param values
 * @param columns
 * @param additional
 * @param titleId
 * @param containerId
 */
function smileTableOpen(title, values, columns, additional, titleId, containerId)
{
    smileTableValues     = values;
    smileTableColumns    = columns;
    smileTableAdditional = additional;
    smileTableContainer  = document.getElementById(containerId);

    document.getElementById(titleId).innerHTML = title;

    smileTableSort(Object.keys(smileTableColumns)[0], 'asc');
}

/**
 * Close a table
 */
function smileTableClose()
{
    smileTableContainer.innerHTML = '';

    smileTableValues        = [];
    smileTableColumns       = [];
    smileTableAdditional    = null;
    smileTableContainer     = null;
    smileTableCurrentColumn = null;
    smileTableCurrentOrder  = null;
}

/**
 * Sort a table
 *
 * @param column
 * @param order
 */
function smileTableSort(column, order)
{
   smileTableCurrentColumn = column;
   smileTableCurrentOrder = order;

   smileTableValues.sort(
       function (rowA, rowB) {

           var a = rowA[smileTableCurrentColumn]['value'];
           var b = rowB[smileTableCurrentColumn]['value'];

           var isDecimal = (rowA[smileTableCurrentColumn]['css_class'].substring(0, 15) === 'st-value-number'
               || rowA[smileTableCurrentColumn]['css_class'].substring(0, 13) === 'st-value-time'
               || rowA[smileTableCurrentColumn]['css_class'].substring(0, 13) === 'st-value-size'
           );

           if (isDecimal) {
                a = parseFloat(a);
                b = parseFloat(b);
           }

           if (a<b) {
               return ((smileTableCurrentOrder==='asc') ? -1 : 1);
           }

           if (a>b) {
               return ((smileTableCurrentOrder==='asc') ? 1 : -1);
           }

           return 0
       }
   );

   smileTableDisplay();
}

/**
 * Display a table
 */
function smileTableDisplay()
{
    var html = '';
    var columnKey;
    var valuesKey;
    var values;
    var nbColumns = 0;
    var needHjs = false;

    html+= '<table class="smile-table">';

    html+= '<thead>';
    html+= '<tr>';
    for (columnKey in smileTableColumns) {
        nbColumns++;

        var column = smileTableColumns[columnKey];
        var columnLabel = column['label'];
        var columnWidth = column['width'];

        html+= '<th';
        if (columnWidth) {
            html+= ' style="width: '+columnWidth+';"'
        }
        html+= '>';
        html+= columnLabel;
        html+= '<span class="st-sortable">';
        if (smileTableCurrentColumn === columnKey && smileTableCurrentOrder === 'asc') {
            html+= '<span class="st-sortable-asc selected">&#x25B2;</span>';
        } else {
            html+= '<span class="st-sortable-asc" onclick="smileTableSort(\''+columnKey+'\', \'asc\');">&#x25B2;</span>';
        }
        if (smileTableCurrentColumn === columnKey && smileTableCurrentOrder === 'desc') {
            html += '<span class="st-sortable-desc selected">&#x25BC;</span>'
        } else {
            html += '<span class="st-sortable-desc" onclick="smileTableSort(\'' + columnKey + '\', \'desc\');">&#x25BC;</span>';
        }
        html+= '</span>';
        html+= '</th>';
    }
    html+= '</tr>';
    html+= '</thead>';

    html+= '<tbody>';
    for (valuesKey = 0; valuesKey < smileTableValues.length; valuesKey++) {
        html+= '<tr';
        if (smileTableAdditional) {
            html+= ' onclick="smileTableToggleRow(\'smile-table-row-'+valuesKey+'\');" class="smile-has-sub-table"'
        }
        html+= '>';
        values = smileTableValues[valuesKey];
        for (columnKey in smileTableColumns) {
            if (values[columnKey]['css_class'].substring(0, 9) === 'hjs-code') {
                needHjs = true;
            }

            html+= '<td class="'+values[columnKey]['css_class']+'">' + values[columnKey]['value'] + '</td>';
        }
        html+= '</tr>';

        if (smileTableAdditional) {
            html+= '<tr class="smile-sub-table" id="smile-table-row-'+valuesKey+'">';
            html+= '<td colspan="'+nbColumns+'" ><div>'+values[smileTableAdditional]+'</div></td>';
            html+= '</tr>';
        }
    }
    html+= '</tbody>';

    html+= '</table>';

    smileTableContainer.innerHTML = html;

    if (needHjs) {
        setTimeout('smileToolbarHighlight();', 100);
    }
}

/**
 * toggle a table row
 *
 * @param rowKey
 */
function smileTableToggleRow(rowKey)
{
    var obj = document.getElementById(rowKey);

    obj.style.display = ((obj.style.display === 'table-row') ? 'none' : 'table-row');
}
