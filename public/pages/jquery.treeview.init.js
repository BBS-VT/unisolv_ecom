/*
 Template: Dastone - Bootstrap 4 admin Dashboard
 Author: Mannatthemes
 File: Treeview
 */



$(function () {
	"use strict";

	// Default
	$('#jstree').jstree();

	//Check Box
	$('#jstree-checkbox').jstree({
		"checkbox" : {
			"keep_selected_style" : false
		  },
		  "plugins" : [ "checkbox" ]
	});
});
