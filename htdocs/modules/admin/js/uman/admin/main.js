dojo.provide("uman.admin.main");

dojo.require("dijit.Dialog");

dojo.require("dijit.form.Form");
dojo.require("dijit.form.Textarea");
dojo.require("dijit.form.DateTextBox");
dojo.require("dijit.form.ComboBox");
dojo.require("dijit.form.CheckBox");
dojo.require("dojo.data.ItemFileReadStore");
dojo.require("dijit.form.FilteringSelect");
dojo.require("dijit.form.NumberSpinner");
dojo.require("dojox.form.DropDownSelect");
dojo.require("dijit.form.MultiSelect");
dojo.require("dojox.form.FileInput");
dojo.require("uman.form.CheckedMultiSelect");

dojo.require("uman.admin.info");
dojo.require("uman.admin.rights");
dojo.require("uman.admin.changes");
dojo.require("uman.admin.editorDialog");
dojo.require("uman.admin.exportDialog");
dojo.require("uman.admin.importDialog");
dojo.require("uman.admin.questionsImportDialog");

dojo.declare("uman.admin.main", null, {
	modes: [
		{manClass: 'uman.admin.info', label: 'Node info'}, 
		{manClass: 'uman.admin.rights', label: 'Rights editor'},
		{manClass: 'uman.admin.changes', label: 'Changes info'}
	],

	currentMode: null,
	currentItem: null,
	currentManObj: null,

	editor: null,

	lastActionsUpdateItem: null,

 	constructor: function() {
 		this._createModesMenu();
//		dojo.connect(nav, 'onClick', dojo.hitch(this, 'onSelectItem'));
//		nav.onClick = dojo.hitch(this, 'onSelectItem');
		dojo.connect(nav, '_onNodeFocus', dojo.hitch(this, 'onNodeFocus'));
		dojo.connect(nav, 'onDblClick', dojo.hitch(this, 'edit'));
		dojo.connect(nodeContent, 'onLoad', dojo.hitch(this, '_onLoadNodeContent'));
 		this.setMode(0);
 		this.editor = new uman.admin.editorDialog();
	},

	setMode: function(modeNum/*, func*/) {
		if(this.currentMode == this.modes[modeNum]) return;
		this.currentMode = this.modes[modeNum];
		this.currentManObj = this.currentMode['manageObject'];
		
		if(!this.currentManObj) {
			this.currentManObj = eval('new ' + this.currentMode['manClass'] + '()');
			this.currentMode['manageObject'] = this.currentManObj;
		}

		this.currentManObj.setMode();
	},
	
 	_createModesMenu: function() {
		dojo.forEach(this.modes, function(mode, idx) {
//			console.debug(mode);
//			var menuItem = new dijit.MenuItem({label: mode['label']/*, iconClass: mode['icon']*/, parentMenu: modesMenu, onClick: dojo.hitch(this, 'setMode', idx)});
//			dojo.connect(menuItem, 'onClick', dojo.hitch(this, 'setMode', idx));
			modesMenu.addChild(new dijit.MenuItem({label: mode['label']/*, iconClass: mode['icon']*/, parentMenu: modesMenu, onClick: dojo.hitch(this, 'setMode', idx)})); 
		}, this);
	},

	loadActions: function(actions) {
		addMenu.destroyDescendants();
		dojo.forEach(actions, function(action){
//			var menuItem = new dijit.MenuItem({label: action['TITLE']});
//			dojo.connect(menuItem, "onClick", dojo.hitch(this, "add", action['NTID'])); 
			addMenu.addChild(new dijit.MenuItem({label: action['TITLE'], onClick: dojo.hitch(this, "add", action['NTID'])}));
		}, this);
	},

	refreshActions: function(item) {
		addMenu.destroyDescendants();
		if(store.getValue(item, 'T_IS_FOLDER')
				&& (!this.lastActionsUpdateItem || (store.getIdentity(item) != store.getIdentity(this.lastActionsUpdateItem)))) {
			adminRPC.getAllowedTypes(store.getIdentity(item)).addCallback(dojo.hitch(this, 'loadActions')).addErrback(error);
		}
		this.lastActionsUpdateItem = item;
	},

	onNodeFocus: function (node){
		this.onSelectItem(node.item);
	},	
	
	onSelectItem: function (item){
		this.refreshActions(item);
		dijit.byId('edit').set('disabled', item == null);
		dijit.byId('delete').set('disabled', item == null);

		var nt = (item.root || item == null) ? '' : store.getValue(nav.lastFocused.item,"T_NODE_KEY");
		dijit.byId('qimp').set('disabled', nt != 'T_WTEST_THEME');
		dijit.byId('pexp').set('disabled', nt != 'T_CHAIR' && nt != 'T_FOLDER_CONTENT');
		
		if(this.currentManObj) this.currentManObj.navItemSelect(item.root ? null : item);
	},

	onNodeDblClick: function(e) {
		e.preventDefault();
		this.edit();
	},	

	_onLoadNodeContent: function() {
		if(!this.currentManObj) {
			this.currentManObj = eval('new ' + this.currentMode['manClass'] + '()');
			this.currentMode['manageObject'] = this.currentManObj;
		}	
	
		this.currentManObj.loadedContent();
	},

	navRefresh: function(parentRefresh) {
//		store.fetchItemByIdentity({identity: store.getIdentity(nav.lastFocused.item)});
		var id = parentRefresh ? store.getIdentity(nav.lastFocused.item) : store.getValue(nav.lastFocused.item,"PARENTID");
		var n = nav._itemNodesMap[id][0];
		if(!parentRefresh) {
//			nav._onItemDelete(nav.lastFocused.item);
			var identity = store.getIdentity(nav.lastFocused.item);
			var en = nav._itemNodesMap[identity][0];
			n.removeChild(en);
			delete nav._itemNodesMap[identity];
			en.destroyRecursive();
		}
//		console.debug(n);
		n.state = "UNCHECKED";
		delete n._expandNodeDeferred;
//		n.isExpanded = false;
		n.isExpandable = true;
		n._setExpando(true);
		nav._expandNode(n);
	},

	haveModRight: function(doAlert) {
		if(!store.getValue(nav.lastFocused.item, 'ALLOW_MOD')) {
			if(doAlert) alert("You do not have right to modify this node");
			return false;
		}
		return true;
	},
	
	add: function(ntid) {
		if(!this.haveModRight(true)) return;
		this.editor.add(store.getIdentity(nav.lastFocused.item), ntid);
	},

	edit: function() {
		if(!this.haveModRight(true)) return;
		this.editor.edit(store.getIdentity(nav.lastFocused.item), store.getValue(nav.lastFocused.item, 'NODETYPEID'));
	},
		
	delNode: function (nextnode, item) {
		store.deleteItem(item);
		nav.focusNode(nextnode || nav._getRootOrFirstNode());
	},

	del: function () {
		if(!this.haveModRight(true)) return;

		if(confirm('Are you shure to delete "' + store.getValue(nav.lastFocused.item, 'TITLE') + '"?')) {
			if(nav.lastFocused.isExpandable && !confirm('Attention! This folder has childs. Are you shure?')) return;
			adminRPC.delNode(store.getIdentity(nav.lastFocused.item)).addCallback(dojo.hitch(this, 'delNode', nav._getNextNode(nav.lastFocused), nav.lastFocused.item)).addErrback(error);
		}
	},

	pexp: function() {
		if(!this.haveModRight(true)) return;
		var exportDlg = new uman.admin.exportDialog();
		exportDlg.execute(store.getIdentity(nav.lastFocused.item));
//		window.open('/admin/export?id=' + store.getIdentity(nav.lastFocused.item), '_blank');
	},	

	pimp: function() {
		if(!this.haveModRight(true)) return;
		var importDlg = new uman.admin.importDialog();
		importDlg.execute();
	},

	qimp: function() {
		if(!this.haveModRight(true)) return;
		var importDlg = new uman.admin.questionsImportDialog();
		importDlg.execute(store.getIdentity(nav.lastFocused.item));
	}

});