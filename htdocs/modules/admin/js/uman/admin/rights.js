dojo.provide("uman.admin.rights");

dojo.declare("uman.admin.rights", null, {
	rightsActions: [
	{icon: 'r_non.png', label: 'inherit', id: -1}, 
	{icon: 'r_res.png', label: 'reset', id: 0}, 
	{icon: 'r_set.png', label: 'set', id: 1}], 
//	{icon: 'r_set_p.png', label: 'set personal', id: 2},
	
	userid: null,
	username: null,
	accessid: null,
	fullRights: false,
	
	rightsMenu: null,
	clickedImg: null,

	
	constructor: function(){
		this.createMenu();
	},

	setMode: function() {
		nodeContent.set('href','/admin/editor/rights');
	},	

	onRightsMenuExecute: function(args) {
//		console.debug(target);
		this.clickedImg = args.target;	
	},

	setRight: function(allow) {
		var params = this.clickedImg.id.split('_');
		var nodeid = params[1];
		var rightid = params[2];
		adminRPC.setRight(nodeid, this.userid, rightid, this.accessid, allow).addErrback(error);
		this.clickedImg.src = '/modules/admin/img/' + this.rightsActions[allow + 1]['icon'];
	},
	
	createMenu: function() {
		this.rightsMenu = new dijit.Menu({leftClickToOpen: true});

		var re = /\.png/g;
		dojo.forEach(this.rightsActions, dojo.hitch(this, function(act) {
			var menuItem = new dijit.MenuItem({label: act['label'], iconClass: act['icon'].replace(re, ''), parentMenu: this.rightsMenu});
			dojo.connect(menuItem, "onClick", dojo.hitch(this, "setRight",	act['id'])); 
			this.rightsMenu.addChild(menuItem);
		}));

		dojo.connect(this.rightsMenu, "_openMyself", dojo.hitch(this, "onRightsMenuExecute")); 
//		dojo.connect(this.rightsMenu, "onExecute", dojo.hitch(this, "onRightsMenuExecute")); 
	},


	loadRights: function (data) {
		var html = '<h2>' + this.username + '</h2><div style="padding-left:5px"><table id="nodes_rights" border="1"><tr><th>title</th>';
		
		for(var r = 0; r < data['rightlist'].length; r++) {
			html += '<th class="rightcol">' + data['rightlist'][r]['TITLE'] + '</th>';
		}

		html += '</tr>';
		
		for(var i = 0; i < data['content'].length; i++) {
			html += '<tr><td class="title">' + data['content'][i]['TITLE'] + '</td>';
			var alr = data['content'][i]['ALLOWRIGHTS'].split(',');
			var ur = dojo.fromJson('{' + data['content'][i]['RIGHTS'] + '}');
			for(var r = 0; r < data['rightlist'].length; r++) {
				var rightPlace = '';
				var rid = data['rightlist'][r]['RIGHTID'];
				if(dojo.indexOf(alr, rid) != -1) {
					var allow = ur ? (isNaN(ur['r' + rid]) ? -1 : ur['r' + rid]) : -1;
					rightPlace = '<a href="#"><img src="/modules/admin/img/' + this.rightsActions[allow + 1]['icon'] + '" id="r_' + data['content'][i]['NODEID'] + '_' + rid + '"/></a>';
				}	
				html += '<td>' + rightPlace + '</td>';
			}
		}

		html += '</table></div>';
		rightsContent.set("content",html);

//		var menu = createMenu();
		if(!this.fullRights)
			dojo.query("#nodes_rights img").forEach(dojo.hitch(this, function(node, index, arr){this.rightsMenu.bindDomNode(node);}));
	},
	
	
	navItemSelect: function (item) {
		this.accessid = dijit.byId("access").getValue();
		this.fullRights = dijit.byId("fullRights").get('value');
		if(this.userid && this.accessid)
			adminRPC.getRightsContent(this.userid, nav.model.store.getIdentity(item), 
					this.accessid, this.fullRights).addCallback(dojo.hitch(this, 'loadRights')).addErrback(error);
	},

	setUser: function (id, name) {
		this.userid = id;
		this.username = name;
		this.refresh();
	},
	
	refresh: function () {
		this.navItemSelect(nav.lastFocused.item);
	},
	
	loadedContent: function () {}
	
});