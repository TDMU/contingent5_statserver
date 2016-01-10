dojo.provide("uman.admin.questionsImportDialog");

dojo.require("dojo.io.iframe");

dojo.declare("uman.admin.questionsImportDialog", null, {
	
	dialog: null,
	formName: 'importForm',

	execute: function(pid) {
		this.dialog = new dijit.Dialog({title: 'Import'});
		this.dialog.onLoad = dojo.hitch(this, 'onLoadedDialog');
		this.dialog.onCancel = dojo.hitch(this, 'onCancelDialog');

		this.dialog.attr('content', '<h1>Import</h1><form dojoType="dijit.form.Form" jsId="importForm" id="importForm" name="importForm" method="POST" enctype="multipart/form-data">'
				+ '<input id="pid" type="hidden" name="pid" value="' + pid + '"/>'
				+ '<input id="questionsFile" type="file" name="questionsFile"/><br/><br/>'
				+ '<input id="save" name="save" value="Сохранить" type="submit" label="Сохранить" dojoType="dijit.form.Button"/></form>'
				+ '<div id="formError"></div>');
		this.dialog.show();
	},

	onLoadedDialog: function () {
		importForm.onSubmit = dojo.hitch(this, 'formSubmit');
	},
	
	onCancelDialog: function () {
		this.dialog.hide();
		this.dialog.destroyRecursive();
		this.dialog = null;
	},

	formSubmit: function (e) {
//		alert('ok');
		e.preventDefault();
		
		if(importForm.validate()) {

			dojo.io.iframe.send({  
				handleAs: "html",
				form: 'importForm',
				url: "/admin/questionsImport",  
//				method: "POST",
//				enctype: "multipart/form-data",
				load: dojo.hitch(this, function(response, args) {
					var data = response.body.innerHTML;  
					if(data == 'true') {
						admin.navRefresh(true);
//						this.nodeid = null;
						this.onCancelDialog();
					} else {
						console.warn("error!", data);
						dojo.byId('formError').innerHTML = data;
					}
					return response;
				}),  
				error: function(response) {  
					console.warn("error!", response);
					dojo.byId('formError').innerHTML = response;
					return response;
				}  
			});
		}
		return false;
	}
	
});