dojo.provide("uman.admin.editorDialog");

dojo.require("dojo.io.iframe");

dojo.declare("uman.admin.editorDialog", null, {
	
	dialog: null,
	formName: 'nodeEditorForm',
	formURL: '/admin/editor/show',
	nodeid: null,
	mode: null,

	showDialog: function(href) {
		this.dialog = new dijit.Dialog({id: 'editorDialog', title: 'Editor'});
		this.dialog.set("href", href);
		this.dialog.onDownloadEnd = dojo.hitch(this, 'onLoadedDialog');
		this.dialog.onCancel = dojo.hitch(this, 'onCancelDialog');
		this.dialog.preventCache = true;
		this.dialog.show();
	},
	
	add: function(pid, ntid) {
		this.nodeid = null;
		this.showDialog(this.formURL + '?PARENTID=' + pid + '&NODETYPEID=' + ntid);
	},

	edit: function(id, ntid) {
//		console.log('edit');
		this.nodeid = id;
		this.showDialog(this.formURL + '?NODEID=' + id + '&NODETYPEID=' + ntid);
	},
	
	onLoadedDialog: function () {
		try {
			var form = dijit.byId(this.formName);
			if(form) {
				if(dojo.byId('dialogScripts')) dojo.query("script", dojo.byId('dialogScripts')).forEach(function(n){eval(n.innerHTML + '\nonFormCreate();');});
				dojo.mixin(form, {
		            onValidStateChange : function(formIsValid) {dijit.byId('save').set('disabled', !formIsValid);}});
				dijit.byId('save').set('disabled', !form.isValid());
				form.onSubmit = dojo.hitch(this, 'formSubmit');
			}
		} catch (e) {
			console.error(e);
		}
	},
	
	onCancelDialog: function () {
//		console.debug(this.dialog);
		if(this.nodeid)
			adminRPC.clearNodeTemp(this.nodeid);
		
		if(typeof(ckeditor) !== 'undefined') {
			ckeditor.destroy();
			ckeditor = undefined;
		}
		
		this.dialog.hide();
		this.dialog.destroyRecursive();
		this.dialog = null;
	},
	

	clearFormErrors: function () {
		var myForm = dojo.byId(this.formName);
		var elements = myForm.elements;
		for (var i = 0; i < elements.length; i++) {
			if (dojo.byId(elements[i].name + "_error"))
				dojo.byId(elements[i].name + "_error").innerHTML = "";
			else if(dojo.byId(elements[i].name)) {
				var div = document.createElement("div");
				div.setAttribute("id", elements[i].name + "_error");
				dojo.byId(elements[i].name).parentNode.appendChild(div);
			}
		}
	},
	
	formSubmit: function (e) {
		e.preventDefault();
		
/*		var cnt = 0;
		for(key in CKEDITOR.instances) ++cnt;
		if(cnt > 0 && !editorReady) return;*/
		
		var allOK = true;
		if(dojo.byId('dialogScripts')) dojo.query("script", dojo.byId('dialogScripts')).forEach(function(n){
			if(!eval(n.innerHTML + '\nbeforeSubmit();')) allOK = false;
		});
		if(!allOK) return;
		
		if(nodeEditorForm.validate()) {
//			for ( i = 0; i < parent.frames.length; ++i ) { // Для сохранения значения ВСЕХ FCKEditor в AJAX
//				if ( parent.frames[i].FCK ) parent.frames[i].FCK.UpdateLinkedField();
//			}

//			console.debug(2);
//			console.debug(
			dojo.io.iframe.send({  
				handleAs: "html",
				form: this.formName, //'nodeEditorForm',
				url: dijit.byId(this.formName).action, // "/admin/editor/save",  
//				url: "http://qqq/admin/editor/save",  
//				method: "POST",
//				enctype: "multipart/form-data",
//				timeoutSeconds: 1200,
/*				handle: function(response, ioArgs){
					console.debug('handle');
					if(response instanceof Error){
					} else {
						
					}
					console.debug(response);
				},*/
				load: dojo.hitch(this, function(response, args) {
//					console.debug('load');
//					console.debug(response);
//					console.debug(args);
//					alert('load');
					var data = response.body.innerHTML;  
					if(data == 'true') {
						admin.navRefresh(!this.nodeid);
						this.nodeid = null;
						this.onCancelDialog();
					} else {
						try {
							var json = dojo.fromJson(data);
							this.clearFormErrors();
							for (elem in json) {
								var msg = '<ul>';
								for (err in json[elem])
									msg += '<li>' + json[elem][err] + '</li>';
								msg += '</ul>';
								dojo.byId(elem + '_error').innerHTML = msg;
							}
						} catch (e) {
//							console.debug(data);
							dojo.byId('formError').innerHTML = data; //e + '<br>' + data;
						}
					}
	
					return response;
				}),  
				error: function(response) {
					console.error("error!", response);
					dojo.byId('formError').innerHTML = response;
					return response;
				}
//				timeout: function(type, data, evt) { reportTimeOutError(type, data, evt);
			});
//			console.debug(3);

		}
//		console.debug(4);

		return false;
	}
	
});