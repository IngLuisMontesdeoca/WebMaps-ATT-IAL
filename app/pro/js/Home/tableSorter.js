/*******************************************************************
*																   *	
*@autor:       (AC) Alberto Cepeda  <jorge.cepeda@webmps.com.mx>   *
*@version:		1.0												   *
*@created:		28/02/2014										   *
*@descripcion:	metodos para plugin tablesort					   *
*@notes:														   *
*																   *	
********************************************************************/

var tableEquiposIniHtml = '<table class="tablesorter cssEqTable" id="tblEquipos">'+
                                            '<thead>'+
                                                '<tr>'+
                                                    '<th class="filter-false cssNEV01" data-placeholder="Filtro">#</th>'+
                                                    '<th class="filter-false sorter-false cssNEV02" data-placeholder="Filtro"><input id = "chkAdmEquiposAll" type="checkbox" class="cssChckAll"></th>'+
                                                    '<th class="cssNEV03" data-placeholder="Filtro">PTN</th>'+
                                                    '<th class="cssNEV04" data-placeholder="Filtro">Cuenta</th>'+
                                                    '<th class="cssNEV05" data-placeholder="Filtro">Cliente</th>'+
                                                    '<th class="cssNEV06" data-placeholder="Filtro">Red</th>'+
                                                    '<th class="cssNEV07" data-placeholder="Filtro">Plan</th>'+
                                                    '<th class="cssNEV08" data-placeholder="Filtro">Servicio</th>'+
                                                    '<th class="cssNEV08" data-placeholder="Filtro">Fecha de corte</th>'+
                                                    '<th class="cssNEV09" data-placeholder="Filtro">Estatus</th>'+
                                                    '<th class="filter-false sorter-false cssNEV10" data-placeholder="Filtro">Opciones</th>'+
                                                '</tr>'+
                                            '</thead>'+
                                            '<tbody id="tBodyTblEquipos">'+
                                            '</tbody>'+
                                        '</table>';
                                
var tableReportesMensajeIniHtml = '<table class="tablesorter cssEqTable" id="tblReportes">'+
                                                    '<thead>'+
                                                        '<tr>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">PTN</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Fecha</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Numero</th>'+
                                                            '<th class="cssLogs05" data-placeholder="Filtro">Mensaje</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Estatus</th>'+
                                                        '</tr>'+
                                                    '</thead>'+
                                                    '<tbody id="tBodyTblReportesMensaje">'+
                                                    '</tbody>'+
                                            '</table>';       
                                
var tableReportesPaymentIniHtml = '<table class="tablesorter cssEqTable" id="tblReportes">'+
                                                    '<thead>'+
                                                        '<tr>'+
                                                            '<th class="cssLogs01" data-placeholder="Filtro">PTN</th>'+
                                                            '<th class="cssLogs01" data-placeholder="Filtro">Fecha</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Tipo de cobro</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Contrato</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Monto</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">Estatus</th>'+
                                                            '<th class="cssLogs04" data-placeholder="Filtro">TransactionID</th>'+
                                                        '</tr>'+
                                                    '</thead>'+
                                                    '<tbody id="tBodyTblReportesPayment">'+
                                                    '</tbody>'+
                                            '</table>';                                       
                                

function adminEquipos_inicializarTableSort(idTable, idContPager, idCboRegistrosPag, idDiv){        
        
	"use strict";

	$.tablesorter.addWidget({
		id: "cssStickyHeaders",
		priority: 10,
		options: {
			cssStickyHeaders_offset     : 0,
			cssStickyHeaders_addCaption : false,
			cssStickyHeaders_attachTo   : null
		},
		init : function(table, thisWidget, c, wo) {
			var $attach = $(wo.cssStickyHeaders_attachTo),
				namespace = '.cssstickyheader',
				$thead = c.$table.children('thead'),
				$caption = c.$table.find('caption'),
				$win = $attach.length ? $attach : $('#dvContTablaEquipos'),
                $win2 = $attach.length ? $attach : $('#dvContTableUsuarios'),
                $win3 = $attach.length ? $attach : $('#'+idDiv),	
	            $win4 = $attach.length ? $attach : $('#dvAddTableContacts');								
                        
			$win.bind('scroll resize '.split(' ').join(namespace + ' '), function() {
				var top = $attach.length ? $attach.offset().top : $win.scrollTop(),
				// add caption height; include table padding top & border-spacing or text may be above the fold (jQuery UI themes)
				// border-spacing needed in Firefox, but not webkit... not sure if I should account for that
				captionTop = wo.cssStickyHeaders_addCaption ? $caption.outerHeight(true) +
					(parseInt(c.$table.css('padding-top'), 10) || 0) + (parseInt(c.$table.css('border-spacing'), 10) || 0) : 0,
				bottom = c.$table.height() - $thead.height() - (c.$table.find('tfoot').height() || 0) - captionTop,
				deltaY = top,
				finalY = (deltaY > 0 && deltaY <= bottom ? deltaY : 0),
				// IE can only transform header cells - fixes #447 thanks to @gakreol!
				$cells = $thead.children().children();
				if (wo.cssStickyHeaders_addCaption) {
					$cells = $cells.add($caption);
				}
				$cells.css({
					"transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-ms-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-webkit-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)"
				});
			}); 
			
			$win2.bind('scroll resize '.split(' ').join(namespace + ' '), function() {
				var top = $attach.length ? $attach.offset().top : $win2.scrollTop(),
				// add caption height; include table padding top & border-spacing or text may be above the fold (jQuery UI themes)
				// border-spacing needed in Firefox, but not webkit... not sure if I should account for that
				captionTop = wo.cssStickyHeaders_addCaption ? $caption.outerHeight(true) +
					(parseInt(c.$table.css('padding-top'), 10) || 0) + (parseInt(c.$table.css('border-spacing'), 10) || 0) : 0,
				bottom = c.$table.height() - $thead.height() - (c.$table.find('tfoot').height() || 0) - captionTop,
				deltaY = top,
				finalY = (deltaY > 0 && deltaY <= bottom ? deltaY : 0),
				// IE can only transform header cells - fixes #447 thanks to @gakreol!
				$cells = $thead.children().children();
				if (wo.cssStickyHeaders_addCaption) {
					$cells = $cells.add($caption);
				}
				$cells.css({
					"transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-ms-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-webkit-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)"
				});
			});                        

			$win3.bind('scroll resize '.split(' ').join(namespace + ' '), function() {
				var top = $attach.length ? $attach.offset().top : $win3.scrollTop(),
				// add caption height; include table padding top & border-spacing or text may be above the fold (jQuery UI themes)
				// border-spacing needed in Firefox, but not webkit... not sure if I should account for that
				captionTop = wo.cssStickyHeaders_addCaption ? $caption.outerHeight(true) +
					(parseInt(c.$table.css('padding-top'), 10) || 0) + (parseInt(c.$table.css('border-spacing'), 10) || 0) : 0,
				bottom = c.$table.height() - $thead.height() - (c.$table.find('tfoot').height() || 0) - captionTop,
				deltaY = top,
				finalY = (deltaY > 0 && deltaY <= bottom ? deltaY : 0),
				// IE can only transform header cells - fixes #447 thanks to @gakreol!
				$cells = $thead.children().children();
				if (wo.cssStickyHeaders_addCaption) {
					$cells = $cells.add($caption);
				}
				$cells.css({
					"transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-ms-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-webkit-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)"
				});
			});                        
			                       
			$win4.bind('scroll resize '.split(' ').join(namespace + ' '), function() {
				var top = $attach.length ? $attach.offset().top : $win4.scrollTop(),
				// add caption height; include table padding top & border-spacing or text may be above the fold (jQuery UI themes)
				// border-spacing needed in Firefox, but not webkit... not sure if I should account for that
				captionTop = wo.cssStickyHeaders_addCaption ? $caption.outerHeight(true) +
					(parseInt(c.$table.css('padding-top'), 10) || 0) + (parseInt(c.$table.css('border-spacing'), 10) || 0) : 0,
				bottom = c.$table.height() - $thead.height() - (c.$table.find('tfoot').height() || 0) - captionTop,
				deltaY = top,
				finalY = (deltaY > 0 && deltaY <= bottom ? deltaY : 0),
				// IE can only transform header cells - fixes #447 thanks to @gakreol!
				$cells = $thead.children().children();
				if (wo.cssStickyHeaders_addCaption) {
					$cells = $cells.add($caption);
				}
				$cells.css({
					"transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-ms-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)",
					"-webkit-transform": finalY === 0 ? "" : "translate(0px," + finalY + "px)"
				});
			});  								   
		},
		remove: function(table, c, wo){
			var namespace = '.cssstickyheader';
			$('#dvContTablaEquipos').unbind('scroll resize '.split(' ').join(namespace + ' '));
			c.$table
				.unbind('update updateAll '.split(' ').join(namespace + ' '))
				.children('thead, caption').css({
					"transform": "",
					"-ms-transform" : "",
					"-webkit-transform" : ""
				});
		}
	});
		
        var pagerOptions = null;
        if((idTable != 'tblEquipos') && (idTable != 'tblReportes'))
        {
            pagerOptions = {

                            // target the pager markup - see the HTML block below
                            container: $("#"+idContPager),

                            // use this url format "http:/mydatabase.com?page={page}&size={size}&{sortList:col}"
                            ajaxUrl:  null,

                            // modify the url after all processing has been applied
                            customAjaxUrl: function(table, url) { return url; },

                            // process ajax so that the data object is returned along with the total number of rows
                            // example: { "data" : [{ "ID": 1, "Name": "Foo", "Last": "Bar" }], "total_rows" : 100 }
                            ajaxProcessing: function(ajax){
                                    if (ajax && ajax.hasOwnProperty('data')) {
                                            // return [ "data", "total_rows" ];
                                            return [ ajax.total_rows, ajax.data ];
                                    }
                            },

                            // output string - default is '{page}/{totalPages}'
                            // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
                            output: '{startRow} to {endRow} ({totalRows})',

                            // apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
                            updateArrows: true,

                            // starting page of the pager (zero based index)
                            page: 0,

                            // Number of visible rows - default is 10
                            size: admEquipos_NUMEROREGISTROSVISIBLES,

                            // Save pager page & size if the storage script is loaded (requires $.tablesorter.storage in jquery.tablesorter.widgets.js)
                            savePages : false,

                            // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
                            // table row set to a height to compensate; default is false
                            fixedHeight: true,

                            // remove rows from the table to speed up the sort of large tables.
                            // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                            removeRows: false,



                // css class names of pager arrows
                cssNext: '.next', // next page arrow
                cssPrev: '.prev', // previous page arrow
                cssFirst: '.first', // go to first page arrow
                cssLast: '.last', // go to last page arrow
                cssGoto: '.gotoPage', // select dropdown to allow choosing a page

                cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
                cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option

                // class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
                cssDisabled: 'disabled', // Note there is no period "." in front of this class name
                cssErrorRow: 'tablesorter-errorRow' // ajax error information row

                    };
        }
        else if((idTable == 'tblEquipos'))
        {
            $("#dvContTablaEquipos").html(tableEquiposIniHtml);
            pagerOptions = {

                                // **********************************
                                //  Description of ALL pager options
                                // **********************************

                                // target the pager markup - see the HTML block below
                                container: $("#"+idContPager),

                                // use this format: "http:/mydatabase.com?page={page}&size={size}&{sortList:col}"
                                // where {page} is replaced by the page number (or use {page+1} to get a one-based index),
                                // {size} is replaced by the number of records to show,
                                // {sortList:col} adds the sortList to the url into a "col" array, and {filterList:fcol} adds
                                // the filterList to the url into an "fcol" array.
                                // So a sortList = [[2,0],[3,0]] becomes "&col[2]=0&col[3]=0" in the url
                                // and a filterList = [[2,Blue],[3,13]] becomes "&fcol[2]=Blue&fcol[3]=13" in the url
                                ajaxUrl : urlAdminEquipos,

                                // modify the url after all processing has been applied
                                customAjaxUrl: function(table, url) {
                                    // manipulate the url string as you desire
                                    // url += '&cPage=' + window.location.pathname;
                                    // trigger my custom event
                                    $(table).trigger('changingUrl', url);
                                    // send the server the current page
                                    //console.log(urlAdminEquipos);
                                    return url;
                                },

                                // add more ajax settings here
                                // see http://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings
                                ajaxObject: {
                                  dataType: 'html',
									type: 'POST',
									data: paramAdminEquipos
                                },

                                // process ajax so that the following information is returned:
                                // [ total_rows (number), rows (array of arrays), headers (array; optional) ]
                                // example:
                                // [
                                //   100,  // total rows
                                //   [
                                //     [ "row1cell1", "row1cell2", ... "row1cellN" ],
                                //     [ "row2cell1", "row2cell2", ... "row2cellN" ],
                                //     ...
                                //     [ "rowNcell1", "rowNcell2", ... "rowNcellN" ]
                                //   ],
                                //   [ "header1", "header2", ... "headerN" ] // optional
                                // ]
                                // OR
                                // return [ total_rows, $rows (jQuery object; optional), headers (array; optional) ]
                                ajaxProcessing: function(data){
                                  
									if(callBackAdminEquipos(data))
									{
										var info = JSON.parse(data);
 
										return [ info.registros, info.tBody, info.header, callBackAjaxTablaEquipos];
									}
                                  
                                },

                                // output string - default is '{page}/{totalPages}'; possible variables: {page}, {totalPages}, {startRow}, {endRow} and {totalRows}
                                output: '{startRow} to {endRow} ({totalRows})',

                                // apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
                                updateArrows: true,

                                // starting page of the pager (zero based index)
                                page: 0,

                                // Number of visible rows - default is 10
                                size: admEquipos_NUMEROREGISTROSVISIBLES,

                                // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
                                // table row set to a height to compensate; default is false
                                fixedHeight: false,

                                // remove rows from the table to speed up the sort of large tables.
                                // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                                removeRows: false,

                                // css class names of pager arrows
                                cssNext        : '.next',  // next page arrow
                                cssPrev        : '.prev',  // previous page arrow
                                cssFirst       : '.first', // go to first page arrow
                                cssLast        : '.last',  // go to last page arrow
                                cssPageDisplay : '.pagedisplay', // location of where the "output" is displayed
                                cssPageSize    : '.pagesize', // page size selector - select dropdown that sets the "size" option
                                cssErrorRow    : 'tablesorter-errorRow', // error information row

                                // class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
                                cssDisabled    : 'disabled' // Note there is no period "." in front of this class name

                              }
        }
        else if((idTable == 'tblReportes'))
        {
            if(parseInt(paramAdminEquipos.tipoReporte) == 1)
                $("#dvResultReportes").html(tableReportesMensajeIniHtml);
            else if(parseInt(paramAdminEquipos.tipoReporte) == 2)
                $("#dvResultReportes").html(tableReportesPaymentIniHtml);
            else
            {
                alert('Selecciona el tipo de reporte');
                return;
            }

            pagerOptions = {

                                // **********************************
                                //  Description of ALL pager options
                                // **********************************

                                // target the pager markup - see the HTML block below
                                container: $("#"+idContPager),

                                // use this format: "http:/mydatabase.com?page={page}&size={size}&{sortList:col}"
                                // where {page} is replaced by the page number (or use {page+1} to get a one-based index),
                                // {size} is replaced by the number of records to show,
                                // {sortList:col} adds the sortList to the url into a "col" array, and {filterList:fcol} adds
                                // the filterList to the url into an "fcol" array.
                                // So a sortList = [[2,0],[3,0]] becomes "&col[2]=0&col[3]=0" in the url
                                // and a filterList = [[2,Blue],[3,13]] becomes "&fcol[2]=Blue&fcol[3]=13" in the url
                                ajaxUrl : urlAdminEquipos,

                                // modify the url after all processing has been applied
                                customAjaxUrl: function(table, url) {
                                    // manipulate the url string as you desire
                                    // url += '&cPage=' + window.location.pathname;
                                    // trigger my custom event
                                    $(table).trigger('changingUrl', url);
                                    // send the server the current page
                                    //console.log(urlAdminEquipos);
                                    return url;
                                },

                                // add more ajax settings here
                                // see http://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings
                                ajaxObject: {
                                  dataType: 'html',
									type: 'POST',
									data: paramAdminEquipos
                                },

                                // process ajax so that the following information is returned:
                                // [ total_rows (number), rows (array of arrays), headers (array; optional) ]
                                // example:
                                // [
                                //   100,  // total rows
                                //   [
                                //     [ "row1cell1", "row1cell2", ... "row1cellN" ],
                                //     [ "row2cell1", "row2cell2", ... "row2cellN" ],
                                //     ...
                                //     [ "rowNcell1", "rowNcell2", ... "rowNcellN" ]
                                //   ],
                                //   [ "header1", "header2", ... "headerN" ] // optional
                                // ]
                                // OR
                                // return [ total_rows, $rows (jQuery object; optional), headers (array; optional) ]
                                ajaxProcessing: function(data){
                                  
									if(callBackAdminEquipos(data))
									{
										var info = JSON.parse(data);
 
										return [ info.registros, info.tBody, info.header, callBackAjaxTablaEquipos];
									}
                                  
                                },

                                // output string - default is '{page}/{totalPages}'; possible variables: {page}, {totalPages}, {startRow}, {endRow} and {totalRows}
                                output: '{startRow} to {endRow} ({totalRows})',

                                // apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
                                updateArrows: true,

                                // starting page of the pager (zero based index)
                                page: 0,

                                // Number of visible rows - default is 10
                                size: admEquipos_NUMEROREGISTROSVISIBLES,

                                // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
                                // table row set to a height to compensate; default is false
                                fixedHeight: false,

                                // remove rows from the table to speed up the sort of large tables.
                                // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
                                removeRows: false,

                                // css class names of pager arrows
                                cssNext        : '.next',  // next page arrow
                                cssPrev        : '.prev',  // previous page arrow
                                cssFirst       : '.first', // go to first page arrow
                                cssLast        : '.last',  // go to last page arrow
                                cssPageDisplay : '.pagedisplay', // location of where the "output" is displayed
                                cssPageSize    : '.pagesize', // page size selector - select dropdown that sets the "size" option
                                cssErrorRow    : 'tablesorter-errorRow', // error information row

                                // class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
                                cssDisabled    : 'disabled' // Note there is no period "." in front of this class name

                              }            
            
        }
	
	
	var indexPager =                    '<div><img src="../../../css/tablesorter/first.png" class="first" alt="First" />'+
                                        '<img src="../../../css/tablesorter/prev.png" class="prev" alt="Prev" />'+
                                        '<span class="pagedisplay"></span> <!-- this can be any element, including an input -->'+
                                        '<img src="../../../css/tablesorter/next.png" class="next" alt="Next" />'+
                                        '<img src="../../../css/tablesorter/last.png" class="last" alt="Last" />'+
                                        '<select class="pagesize" id="'+idCboRegistrosPag+'" title="Select page size">'+
			                     		'<option value="10">10</option>'+
            			           		'<option value="20">20</option>'+
            			           		'<option value="30">30</option>'+
            			           		'<option value="50">50</option>'+
                                        '</select>'+
                                        '<select class="gotoPage" title="Select page number"></select></div>'; 
    $("#"+idContPager).html(indexPager);	
    $("#"+idCboRegistrosPag+" option[value='"+admEquipos_NUMEROREGISTROSVISIBLES+"']").prop("selected", true);			 

	//var offSet=$("#"+idTable).offset().top;
	if(idTable == "tblLogs" || idTable == "tblReportes"){
		var flagHeaderCheck = true;		
	}
	else{
		var flagHeaderCheck = false;				
	}
	
 	$("#"+idTable).tablesorter({
		/*theme:'blue',*/
		headers: {
          1: {
                sorter: flagHeaderCheck
        	 }
		},
		widthFixed : true,
		showProcessing: true,
    	headerTemplate : '{content} {icon}', // Add icon for jui theme; new in v2.7!		
		// initialize zebra and filter widgets
      	widgets: ['zebra','cssStickyHeaders','filter'],

      	widgetOptions: {
           cssStickyHeaders_offset     : 0,
           cssStickyHeaders_addCaption : true,
           cssStickyHeaders_attachTo   : null

			
        }
		
	}).tablesorterPager(pagerOptions);        
 			
}