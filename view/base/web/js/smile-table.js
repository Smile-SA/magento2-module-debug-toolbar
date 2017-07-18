var smileTableValues        = [];
var smileTableColumns       = [];
var smileTableAdditional    = [];
var smileTableContainer     = null;
var smileTableCurrentColumn = null;
var smileTableCurrentOrder  = null;

/**
 * Open a table
 *
 * @param values
 * @param columns
 * @param additional
 * @param containerId
 */
function smileTableOpen(values, columns, additional, containerId)
{
    smileTableValues     = values;
    smileTableColumns    = columns;
    smileTableAdditional = additional;
    smileTableContainer  = document.getElementById(containerId);

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
    smileTableAdditional    = [];
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

   var isDecimal = false;

   if (smileTableColumns[column]['class'].substring(0, 13) == 'st-value-unit') {
       isDecimal = true;
   }
   if (smileTableColumns[column]['class'] == 'st-value-number') {
       isDecimal = true;
   }

   smileTableValues.sort(
       function(rowA, rowB)
       {
           var a = rowA[smileTableCurrentColumn];
           var b = rowB[smileTableCurrentColumn];

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
    var column;
    var valuesKey;
    var values;
    var nbColumns = 0;
    var hasAdds = false;

    for (key in smileTableAdditional) {
        hasAdds = true;
    }

    html+= '<table class="smile-table">';

    html+= '<thead>';
    html+= '<tr>';
    for (columnKey in smileTableColumns) {
        nbColumns++;
        column = smileTableColumns[columnKey];
        html+= '<th class="'+column['class']+'">';
        if (smileTableCurrentColumn === columnKey && smileTableCurrentOrder === 'asc') {
            html+= '<span class="selected">&#x25B2;</span>';
        } else {
            html+= '<span onclick="smileTableSort(\''+columnKey+'\', \'asc\');">&#x25B2;</span>';
        }
        if (smileTableCurrentColumn === columnKey && smileTableCurrentOrder === 'desc') {
            html += '<span class="selected">&#x25BC;</span>'
        } else {
            html += '<span onclick="smileTableSort(\'' + columnKey + '\', \'desc\');">&#x25BC;</span>';
        }
        html+= column['title'];
        html+= '</th>';
    }
    html+= '</tr>';
    html+= '</thead>';

    html+= '<tbody>';
    for (valuesKey = 0; valuesKey < smileTableValues.length; valuesKey++) {
        html+= '<tr';
        if (hasAdds) {
            html+= ' onclick="smileTableToggleRow(\'smile-table-row-'+valuesKey+'\');" class="smile-has-sub-table"'
        }
        html+= '>';
        values = smileTableValues[valuesKey];
        for (columnKey in smileTableColumns) {
            column = smileTableColumns[columnKey];
            html+= '<td class="'+column['class']+'"';
            html+= '>';
            html+= smileTableProtectValue(values[columnKey]);
            html+= '</td>';
        }
        html+= '</tr>';

        if (hasAdds) {
            html+= '<tr class="smile-sub-table" id="smile-table-row-'+valuesKey+'">';
            html+= '<td colspan="'+nbColumns+'" >';
            html+= '<table>';
            for (addKey in smileTableAdditional) {
                html+= '<tr>';
                html+= '<th>' + smileTableAdditional[addKey] + '</th>';
                html+= '<td><pre>';
                html+= smileTableProtectValue(JSON.stringify(values[addKey], null, 2));
                html+= '</pre></td>';
                html+= '</tr>';
            }
            html+= '</table>';
            html+= '</td>';
            html+= '</tr>';
        }
    }
    html+= '</tbody>';

    html+= '</table>';

    smileTableContainer.innerHTML = html;
}

/**
 * toogle a table row
 *
 * @param rowKey
 */
function smileTableToggleRow(rowKey)
{
    var obj = document.getElementById(rowKey);

    obj.style.display = ((obj.style.display === 'table-row') ? 'none' : 'table-row');
}

/**
 * Protect a value
 *
 * @param value
 *
 * @returns string
 */
function smileTableProtectValue(value)
{
    value = ''+value;
    return value.replace('<', '&lt;');
}
