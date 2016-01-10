dojo.provide("uman.admin.exportDialog");

dojo.declare("uman.admin.exportDialog", null, {
	
	dialog: null,

	execute: function(id) {
		this.dialog = new dijit.Dialog({title: 'Export'});
		this.dialog.onCancel = dojo.hitch(this, 'onCancelDialog');

		this.dialog.attr('content', '<form dojoType="dijit.form.Form" jsId="exportForm" name="exportForm" method="POST" action="/admin/export">'
				+ '<input type="hidden" name="id" value="' + id + '"/>'
				+ 'Include files <input id="contentinc" type="checkbox" name="contentinc" value="true"/><br/><br/>'
				+ '<input name="save" label="Get package" type="submit" dojoType="dijit.form.Button"/></form>'
				+ '<div id="formError"></div>');
		this.dialog.show();
	},

	onCancelDialog: function () {
		this.dialog.hide();
		this.dialog.destroyRecursive();
		this.dialog = null;
	}
});