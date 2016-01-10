dojo.provide("uman.admin.changes");

dojo.declare("uman.admin.changes", null, {
	userid: null,
	fromDate: null,
	toDate: null,

	loadingMessage: "<span class='dijitContentPaneLoading'>${loadingState}</span>",
	errorMessage: "<span class='dijitContentPaneError'>${errorState}</span>",
	
	constructor: function(){
		var messages = dojo.i18n.getLocalization("dijit", "loading", this.lang);
		this.loadingMessage = dojo.string.substitute(this.loadingMessage, messages);
		this.errorMessage = dojo.string.substitute(this.errorMessage, messages);
	},

	setMode: function() {
		nodeContent.setHref('/admin/editor/changes');
	},	

	loadChanges: function (data) {
//		dojo.debug('test');
		var html = "<table class=\"data\" cellpadding=\"0\" cellspacing=\"0\"><thead><tr><th style=\"width: 30px\">№</th><th style=\"width: 100px\">modified</th><th>page</th><tr></thead><tbody>";
		for(var r = 0; r < data.length; r++) {
			html += '<tr>'
			  + '<td> <a href="javascript:admin.editor.edit(' + data[r]['NODEID'] + ',' + data[r]['NODETYPEID'] + ');' + '">' + (r+1) + '.</a></td>'
			  + '<td title="Modified by: ' + data[r]['MODIFYUSER'] + '   Created by: ' + data[r]['CREATEUSER'] + ' at ' + data[r]['CREATEDATE'] + '">'
			  + data[r]['MODIFYDATE'] +'</td>'
			  + '<td class="left">'
			  + (data[r]['HREF'] ? '<a target="_blank" href="' + data[r]['HREF'] + '">' + data[r]['TITLE'] + '</a>' : data[r]['TITLE'])
			  + '</td></tr>';
		}
		html += "</tbody></table>";
		changesContent.set("content",html);
	},
	
	navItemSelect: function (item) {
		fbFormat = {selector: 'date', datePattern: 'y/M/d', locale: 'ru-ru'};
		this.fromDate = dojo.date.locale.format(dijit.byId("fromDate").attr('value'), fbFormat);
		this.toDate = dojo.date.locale.format(dijit.byId("toDate").attr('value'), fbFormat);
		if(this.fromDate && this.toDate) {
			changesContent.set("content",this.loadingMessage);
			adminRPC.getChangesContent(nav.model.store.getIdentity(item), this.fromDate, this.toDate).addCallback(dojo.hitch(this, 'loadChanges')).addErrback(dojo.hitch(this, 'error'));
		}
	},
	
	error: function () {
		changesContent.set("content",this.errorMessage);
	},

	refresh: function () {
		this.navItemSelect(nav.lastFocused.item);
	},
	
	loadedContent: function () {}
	
});