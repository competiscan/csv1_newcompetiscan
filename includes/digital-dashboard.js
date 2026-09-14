$(document).ready(function() {
	fill_info_container();
	
	$( "#dsave_search_name" ).autocomplete({ 
		minLength: 0,
		source: 'dashboard_save_search.php',
		select: function( event, ui ) {
			if(ui.item.id){
				$( "#dsave_search_id" ).val(ui.item.id);
				populate_info_container('[]');
				populate_info_container(ui.item.data);
				fill_info_container();
				end_ss(1);
			}
		}
	});
	$( "#dsave_search_name" ).on( "click change keyup", function() {
		var sval = $( "#dsave_search_name" ).val();
		if(sval){
			$('#save_dsave_search').show();
		}
	});
	$( "#show_dsave_search" ).on( "click", function() {
		$( "#dsave_search_name" ).autocomplete( "search", "" );
		$( "#dsave_search_name" ).focus();
	});
	$( "#save_dsave_search" ).on( "click", function() {
		var search_string = $("#searchForm").serialize();
		var data_string = 'dsave_search_data='+encodeURIComponent(search_string)+'&dsave_search_name='+encodeURIComponent($( "#dsave_search_name" ).val())+'&dsave_search_id='+encodeURIComponent($( "#dsave_search_id" ).val());
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "dashboard_save_search.php",
			data: data_string,
			beforeSend: function(jqXHR,settings){
				start_ss();
			},
			complete: function(jqXHR,textStatus){
				end_ss(1);
			},
			success: function(data,textStatus,jqXHR) {
				if(data!='-1'){
					$( "#dsave_search_id" ).val(data);
					$( "#clear_dsave_search" ).show();
					$( "#delete_dsave_search" ).show();
					$( "#save_dsave_search" ).show();
				}
			}
		});
	});
	$( "#delete_dsave_search" ).on( "click", function() {
		var data_string = 'delete=1&dsave_search_name='+encodeURIComponent($( "#dsave_search_name" ).val())+'&dsave_search_id='+encodeURIComponent($( "#dsave_search_id" ).val());
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "dashboard_save_search.php",
			data: data_string,
			beforeSend: function(jqXHR,settings){
				start_ss();
			},
			complete: function(jqXHR,textStatus){
				end_ss(0);
			},
			success: function(data,textStatus,jqXHR) {
				$( "#dsave_search_id" ).val('');
				$( "#dsave_search_name" ).val('');
				$( "#clear_dsave_search" ).hide();
				$( "#delete_dsave_search" ).hide();
				$( "#save_dsave_search" ).hide();
			}
		});
	});
	$( "#clear_dsave_search" ).on( "click", function() {
		clear_info_container();
	});
	$( "#electricitynaturalgas" ).on( "change", function() {
		var selected_edc = new Array();
		$("#edc").find('option').each(function() {
			if($(this).prop('selected')){
				selected_edc[$(this).val()] = true;
			}
		});
		var selected_eg = '';
		$("#electricitynaturalgas").find('option').each(function() {
			if($(this).prop('selected')){
				if($(this).val()){
					if(selected_eg!=''){
						selected_eg = selected_eg + ',';
					}
					selected_eg = selected_eg + $(this).val();
				}
			}
		});
		if(selected_eg==''){
			selected_eg = '-1';
		}
		$.ajax({
			type: "GET",
			dataType: "html",
			url: "dashboard_info.php?field=edc&look="+selected_eg,
			success: function(data,textStatus,jqXHR) {
				$("#edc").html(data);
				var none = true;
				$("#edc").find('option').each(function() {
					if(selected_edc[$(this).val()]){
						$(this).prop('selected',true);
						none = false;
						return true;
					}
				});
				if(none){
					$("#edc").prop('selectedIndex',0);	
				}
			}
		});
	});
});

var first_load = true;
var waiting = false;
function start_ss(){
	$('#clear_dsave_search').hide();
	$('#delete_dsave_search').hide();
	$('#show_dsave_search').hide();
	$('#save_dsave_search').hide();
	$( "#dsave_search_name" ).attr('disabled',true);
	$('#waitss').show();
}
function end_ss(showbuttons){
	$('#waitss').hide();
	$( "#dsave_search_name" ).attr('disabled',false);
	$('#show_dsave_search').show();
	if(showbuttons){
		$('#save_dsave_search').attr('value','save');
		$('#save_dsave_search').show();
		$('#clear_dsave_search').show();
		$('#delete_dsave_search').show();
	}
	else{
		$('#save_dsave_search').attr('value','save new');
	}
}
function fill_info_container(){
	var search_string = $("#searchForm").serialize();
	$.ajax({
		type: "POST",
		dataType: "html",
		url: "dashboard_info.php",
		data: search_string,
		beforeSend: function(jqXHR,settings){
			start_info_container();
		},
		complete: function(jqXHR,textStatus){
			end_info_container();
		},
		success: function(data,textStatus,jqXHR) {
			$("#info_container").html(data);
		}
	});
}
function populate_info_container(json_data){
	var data = jQuery.parseJSON(json_data);
	var filter_array = new Array("input", "select");
	for(var j=0;j<filter_array.length;j++){
		$("#searchForm "+filter_array[j]).each(function( index ) {
			var id_val = $( this ).attr('id');
			if(filter_array[j]=='select'){
				var none = true;
				$( this ).prop('selectedIndex',-1);
				
				if(data[id_val]){
					if($( this ).prop('multiple') && data[id_val].length){
						for(var k=0;k<data[id_val].length;k++){
							$( this ).find('option').each(function() {
								if($(this).val()==data[id_val][k]){
									$(this).prop('selected',true);
									none = false;
									return true;
								}
							});
						}
					}
					else{
						$( this ).find('option').each(function() {
							if($(this).val()==data[id_val]){
								$(this).prop('selected',true);
								none = false;
								return true;
							}
						});
					}
				}
				if(none){
					$( this ).prop('selectedIndex',0);	
				}
			}
			else if($( this ).attr('type')=='checkbox'){
				$( this ).prop('checked',false);
				var names = id_val.split('_');
				if(names.length>1 && data[names[0]]){
					for(var k=0;k<data[names[0]].length;k++){
						if(data[names[0]][k]==$( this ).attr('value')){
							$( this ).prop('checked',true);
							break;
						}
					}
				}
			}
			else if($( this ).attr('type')=='hidden'){
				var sliderjq = $( "#"+id_val+"-slider-range" );
				if(sliderjq && sliderjq.length && sliderjq.length>0){
					var min = sliderjq.slider( "option", "min" );
					var max = sliderjq.slider( "option", "max" );
					if(data[id_val] && typeof data[id_val] === 'string'){
						var minmax = data[id_val].split('-');
						if(minmax.length>1){
							min = minmax[0];
							max = minmax[1];
						}
					}
					sliderjq.slider( "values", [ min, max ] );
					sliderjq.trigger( "slidestop" );
				}
				else{
					if(data[id_val]){
						$( this ).attr('value',data[id_val]);
					}
					else {
						$( this ).attr('value','');
					}
				}
			}
			else if($( this ).attr('type')=='text' && $( this ).attr('name')==id_val){
				if($( this ).tokenInput){
					$( this ).tokenInput("clear");
					if(data[id_val] && data[id_val].length){
						for(var k=0;k<data[id_val].length;k++){
							$( this ).tokenInput("add", {id: data[id_val][k]['id'], name: data[id_val][k]['name']});
						}
					}
				}
				else{
					if(data[id_val]){
						$( this ).attr('value',data[id_val]);
					}
					else {
						$( this ).attr('value','');
					}
				}
			}
		});
	}
	$( "#electricitynaturalgas" ).trigger( "change" );
	first_load = true;
}
function clear_info_container(){
	//document.location.href = "dashboard.php";
	$( "#dsave_search_id" ).val('');
	$( "#dsave_search_name" ).val('');
	$( "#clear_dsave_search" ).hide();
	$( "#delete_dsave_search" ).hide();
	$( "#save_dsave_search" ).hide();
	$('#save_dsave_search').attr('value','save new');
	
	populate_info_container('[]');
	fill_info_container();
}
function start_info_container(){
	$('#waitdiv').dialog({ 
		closeOnEscape: false,
		dialogClass: "no-close",
		draggable: false,
		modal: true,
		resizable: false
	});
	waiting = true;
}
function end_info_container(){
	waiting = false;
	try{
		$('#waitdiv').dialog('destroy');
	}
	catch(err){
	}
	if(first_load){
		first_load = false;
	}
	else{
		document.location.href = "#info_container_top";
	}
}
function search_info_container(){
	page_info_container("");
	hideRangeVisualization();
}
function sort_info_container(sort_value){
	if(!waiting){
		document.searchForm.sort_entries.value = sort_value;
		fill_info_container();
	}
}
function page_info_container(start_entries_value){
	if(!waiting){
		document.searchForm.start_entries.value = start_entries_value;
		fill_info_container();
	}
}
function show_csv(){
	var search_string = $("#searchForm").serialize();
	document.location.href = "dashboard_info.php?csv=1&"+search_string;
}
function show_result_detail(row_num) {
	var element = $("#detail_"+row_num);
	var element2 = $("#detail_img_"+row_num);
	if(element && element2){
		if(element.css("display")=="none") { 
			element.css("display","table-row");
			element2.attr("src","images/minus.jpg");
		} 
		else { 
			element.css("display","none"); 
			element2.attr("src","images/plus.jpg");
		}
	}
}
function move_chart_top(){
	document.location.href = "#dashboard_chart_top";
}
function move_page_top(){
	document.location.href = "#";
}
function hideRangeVisualization() {
	var element = $("#dashboard_chart");
	if(element){
		element.css("display","none");
	}
}
function showRangeVisualization() {
	var element = $("#dashboard_chart");
	if(element){
		element.css("display","block");
	}
}

google.load('visualization', '1.1', {packages: ['corechart', 'controls']});
function drawRangeVisualization() {
	if(document.resultForm){
		var columnsArray = new Array();
		columnsArray[0] = new Array();
		columnsArray[0]["calc"] = function(dataTable, rowIndex) {
			return dataTable.getFormattedValue(rowIndex, 0);
		};
		columnsArray[0]["type"] = "string";
		
		var nameArray = new Array();
		var historyArray = new Array();
		var last_historyArray = new Array();
		nameArray.push("Date");
		nameArray.push("Control");
		for(var i=1;i<=document.resultForm.total_rows.value;i++){
			if(document.resultForm["graph_"+i] && document.resultForm["graph_"+i].checked){
				$.ajax({
					async: false,
					dataType: "json",
					url: "dashboard_info.php?json=1&graph_id="+document.resultForm["graph_"+i].value,
					success: function(data,textStatus,jqXHR) {
						nameArray.push(data['name']);
						historyArray.push(data['history']);
						last_historyArray.push(0);
						columnsArray[columnsArray.length] = columnsArray.length+1;
					}
				});
			}
		}
		
		if(nameArray.length>2){
			showRangeVisualization();
			var dataArray = new Array();
			dataArray.push(nameArray);
			var dateArray = new Array();
			var dateSort = new Array();
			for(var m=0;m<historyArray.length;m++){
				for(var n=0;n<historyArray[m].length;n++){
					if(dash_in_array(historyArray[m][n]['Ymd'],dateSort)==-1){
						dateSort[dateSort.length] = historyArray[m][n]['Ymd'];
						dateArray[historyArray[m][n]['Ymd']] = new Date(historyArray[m][n]['y'], historyArray[m][n]['m'], historyArray[m][n]['d']);
					}
				}
			}
			dateSort.sort(function(a,b){return a-b});
			var tot = dateSort.length;
			var first = 0;
			var last = tot-1;
			if(tot>3){
				first = 1;
				last = tot-2;
			}
			if(last<0){
				last = 0;
			}
			var datemin = dateArray[dateSort[first]];
			var datemax = dateArray[dateSort[last]];
			var chartmin = -1;
			var chartmax = 0;
			for(var j=0;j<dateSort.length;j++){
				var d = dateSort[j];
				var tmpArray = new Array();
				tmpArray.push(dateArray[d]);
				tmpArray.push(0); // blank Control data
				for(var m=0;m<historyArray.length;m++){
					var found = false;
					for(var n=0;n<historyArray[m].length;n++){
						if(d==historyArray[m][n]['Ymd']){
							if(chartmin==-1 || historyArray[m][n]['v']<chartmin){
								chartmin = historyArray[m][n]['v'];
							}
							if(historyArray[m][n]['v']>chartmax){
								chartmax = historyArray[m][n]['v'];
							}
							tmpArray.push(historyArray[m][n]['v']);
							last_historyArray[m] = historyArray[m][n]['v'];
							found = true;
							break;
						}
					}
					if(!found){
						tmpArray.push(last_historyArray[m]);
					}
				}
				dataArray.push(tmpArray);
			}
			var data = google.visualization.arrayToDataTable(dataArray);
			
			var control = new google.visualization.ControlWrapper({
				"controlType": "ChartRangeFilter",
				"containerId": "control",
				"options": {
					"filterColumnIndex": 0,
					"ui": {
						"chartType": "LineChart",
						"chartOptions": {
							"chartArea": {"width": "90%"},
							"hAxis": {"baselineColor": "none"}
						},
						"chartView": {
							"columns": [0, 1]
						},
						// 1 day in milliseconds = 24 * 60 * 60 * 1000 = 86,400,000
						"minRangeSize": 86400000
					}
				},
				"state": {
					"range": {"start": datemin, "end": datemax}
				}
			});
			
			var chart_inc = 1;
			var chart_range = chartmax - chartmin;
			if(chart_range<chart_inc){
				chart_inc = chart_range/tot;
			}
			chartmax = chartmax + chart_inc;
			if(chartmin-chart_inc>=0){
				chartmin = chartmin - chart_inc;
			}
			var chart = new google.visualization.ChartWrapper({
				"chartType": "LineChart",
				"containerId": "chart",
				"options": {
					"chartArea": {"height": "80%", "width": "60%"},
					"hAxis": {"slantedText": false},
					"vAxis": {"minValue":chartmin, "maxValue":chartmax}
				},
				// Convert the first column from "date" to "string".
				"view": {
					"columns": columnsArray
				}
			});
			
			var dashboard_chart = new google.visualization.Dashboard(document.getElementById("dashboard_chart"));
			dashboard_chart.bind(control, chart);
			dashboard_chart.draw(data);
			google.visualization.events.addListener(dashboard_chart, 'ready', move_chart_top);
		}
		else{
			hideRangeVisualization();
		}
	}
}
function dash_in_array(val,ar){
	for(var i=0;i<ar.length;i++){
		if(val==ar[i]){
			return i;
		}
	}
	return -1;
}