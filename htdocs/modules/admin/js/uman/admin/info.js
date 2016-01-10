dojo.provide("uman.admin.info");

dojo.declare("uman.admin.info", null, {
	inUpdateContent: false,
	
	constructor: function(){
	},
	
	setMode: function() {
		if(nav.lastFocused) this.navItemSelect(nav.lastFocused.item);
		else nodeContent.set("content",'Node info');
	},
	
	navItemSelect: function (item, node) {
		this.inUpdateContent = true;
		try {
			var html = '<table>';
			dojo.forEach([{f: 'NODEID', t: 'Id'}, {f: 'TITLE', t: 'Title'}, {f: 'NODE_KEY', t: "Node key"}, {f: 'T_TITLE', t: 'Type'}, {f: 'T_NODE_KEY', t: "Type node key"}], function(field) {
				html += '<tr><td style="font-weight:bold; padding-right: 8px; vertical-align:top;">' + field['t'].replace(/ /g, '&#160;') + '</td><td>' + nav.model.store.getValue(item, field['f']) + '</td></tr>';
			});
			html += '<tr><td colspan="2"><hr/></td></tr>';
			html += '<tr><td style="font-weight:bold; padding-right: 8px; vertical-align:top;">Id path</td><td>' + nav.getFullPath(',', 'NODEID', node); + '</td></tr>';
			html += '<tr><td style="font-weight:bold; padding-right: 8px; vertical-align:top;">Path</td><td>' + nav.getFullPath(' / ', 'TITLE', node); + '</td></tr>';
			html += '</table>';
			nodeContent.set("content",html);
		} finally {
			this.inUpdateContent = false;
		}	
	},
	
	loadedContent: function () {}
	
});