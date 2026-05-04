/**
 * @author laurent anezo <laurent.anezo@gmail.com>
 */

(function ($) {
    'use strict';
    var sprintf = $.fn.bootstrapTable.utils.sprintf;
	var order = 1;

    $.extend($.fn.bootstrapTable.defaults, {
        newRecord: false,
		newRecordtable: false,
		exportTable: false,
		filterTable: '',
		tableName: '',
		idParent: '',
		divParent: '',
		other: false
    });

    $.extend($.fn.bootstrapTable.defaults.icons, {
        newRecord: 'fal fa-plus',
		newRecordtable: 'fal fa-plus',
		exportTable: 'fal fa-file-excel',
		filterTable: 'fal fa-filter'
    });

    $.extend($.fn.bootstrapTable.defaults, $.fn.bootstrapTable.locales);

    var BootstrapTable = $.fn.bootstrapTable.Constructor,
        _initToolbar = BootstrapTable.prototype.initToolbar;

    BootstrapTable.prototype.initToolbar = function () {
        order = 1;
		this.showToolbar = this.options.exportTable;
		this.showToolbar = this.options.filterTable;
  		this.showToolbar = this.options.newRecord;
		this.showToolbar = this.options.newRecordtable;
		this.showToolbar = this.options.other;
        _initToolbar.apply(this, Array.prototype.slice.apply(arguments));
		
		// remove class to search input
		this.$toolbar.find('>.search').removeClass("btn-group");
		$("#table-result .search-input").val(sessionStorage.getItem('search'));
		// Create Parent if not exist
		this.options.divParent = this.$toolbar.find('>.btn-group');
		if (!this.options.divParent.length) {
			$('<div class="btn-group btn-group-tools order-1"></div>').appendTo(this.$toolbar);
		}else{
			this.options.divParent.addClass("btn-group-tools order-1");
			$("div[title=\"Colonnes\"]").addClass("order-9");
		}
		
		//newRecord
        if (!this.options.newRecord) {
        	// remove add-button if newRecord = false
			//$("#table-toolbar .btn-add").remove();
			/*
            var that = this,
                $btnGroup = this.$toolbar.find('>.btn-group-tools'),
                $newRecord = $btnGroup.find('div.newRecord');

            if (!$newRecord.length) {
                $newRecord = $([
                    '<div class="newRecord btn-group order-'+order+'">',
                        '<button class="btn' +
                            sprintf(' btn-%s', this.options.buttonsClass) +
                            sprintf(' btn-%s', this.options.iconSize) +
                            '" aria-label="...." ' +
							sprintf('onclick="showCard(\'%s\',\'0\')" ', this.options.tableName) +
                            'title="Nouvel enregistrement" ' +
                            'type="button">',
                            sprintf('<i class="%s"></i> ', this.options.icons.newRecord),
                        '</button>',
                    '</div>'].join('')).prependTo($btnGroup);
				order ++;
            }
			*/
        }
		
		//newRecordtable
        if (this.options.newRecordtable) {
            var that = this,
                $btnGroup = this.$toolbar.find('>.btn-group'),
                $newRecordtable = $btnGroup.find('div.newRecordtable');

            if (!$newRecordtable.length) {
                $newRecordtable = $([
                    '<div class="newRecordtable btn-group order-'+order+'">',
                        '<button class="btn btn-outline-success ml-2" aria-label="...." ' +
							sprintf('onclick="tableAction({tableName:\'%s\',idrecord:\'%s\',action:\'newCardTable\'})" ', this.options.tableName, this.options.idParent) +
                            'title="Nouveau" ' +
                            'type="button">',
                            sprintf('<i class="%s"></i> ', this.options.icons.newRecordtable),
                        '</button>',
                    '</div>'].join('')).prependTo($btnGroup);
				order ++;
            }
        }
						
		//exportTable
		if (this.options.exportTable) {
            var that = this,
                $btnGroup = this.$toolbar.find('>.btn-group-tools'),
                $exportTable = $btnGroup.find('div.exportTable');
			
            if (!$exportTable.length) {
                $exportTable = $([
                    '<div class="exportTable btn-group order-'+order+'">',
                        '<button class="btn' +
                            sprintf(' btn-%s', this.options.buttonsClass) +
                            sprintf(' btn-%s', this.options.iconSize) +
                            '" aria-label="...." ' +
							sprintf('onclick="exportExcel(\'%s\')" ', this.options.tableName) +
                            'title="Exporter en xlsx" ' +
                            'type="button">',
                            sprintf('<i class="%s"></i> ', this.options.icons.exportTable),
                        '</button>',
                    '</div>'].join('')).prependTo($btnGroup);
				order ++;
            }
        }
		
		//filterTable
		if (this.options.filterTable != '') {
            var that = this,
                $btnGroup = this.$toolbar.find('>.btn-group-tools'),
                $filterTable = $btnGroup.find('div.filterTable'),
				$filters = this.options.filterTable.split(","),
				$file = '',
				$post = '',
				$tableName = this.options.tableName,
				$selection = this.options.selection,
				i;
			for (i = 0; i < $filters.length; i++) {
				callRouter($tableName, (order+i), "filter", {table:$filters[i]}, function(result){
					if(result){
						switch(result.code){
							case 0 : 
								infoContent.html(result.html);
								break;
							case 1 : 
								$filterTable = $(result.html).appendTo($btnGroup);
								var select = $('.bootstrap-table .btn-group '+jq(result.info,"#"));
								select.addClass("order-"+(order+parseInt(result.order)));
								select.selectpicker({
									iconBase : "fal",
									tickIcon : "fa-check",
									//liveSearch : true,
									//actionsBox : "true",
									//selectAllText : "Tous",
									//deselectAllText : "Aucun"
								});
								
								// Select user selection
							    var attr = $selection[result.info];
								select.selectpicker('val', attr);
								
								// onchange
								select.on('changed.bs.select', function (e) {
									$(this).data("changed","1");
								});
								
								// onhidden
								select.on('hide.bs.select', function (e) {
									if($(this).data("changed") == 1){
										//select.selectpicker('destroy');
										var search = $("#table-result .search-input").val();
										if(search.length > 0){
											sessionStorage.setItem('search', search);
										}else{
											sessionStorage.removeItem('search');
										}
										showTable($tableName,'fullTable','showCard', function(){
											$(".show-tick").remove();
											$("#table-result .search-input").trigger('blur');
										});
									}
								});
								break;
							case "wor_periode" : 
								$filterTable = $(result.html).appendTo($btnGroup);
								$(jq(result.info,"#")).removeAttr("multiple");
								var select = $('.bootstrap-table .btn-group '+jq(result.info,"#"));
								select.addClass("order-"+(order+parseInt(result.order)));
								select.selectpicker({
									iconBase : "fal",
									tickIcon : "fa-check",
									liveSearch : false,
									//actionsBox : "true",
									//selectAllText : "Tous",
									//deselectAllText : "Aucun"
								});
								
								// Select user selection
							    var attr = $selection[result.info];
								select.selectpicker('val', attr);
								
								// onchange
								select.on('changed.bs.select', function (e) {
									table_action({tablename:'wor_attendance',action:'weekTable'})
								});
								break;
							default :
								infoContent.html("Error "+result.code+"<br>"+result.html+"<br>"+result.file+" "+result.line);
						}
					}

				});

			}
        }
    };
})(jQuery);