dojo.provide("uman.Tree");

dojo.require("dijit.Tree");

dojo.declare("uman.Tree", dijit.Tree, {
	
	getPath: function (separator, field, node) {
		var path = '';
		node = node ? node : this.lastFocused;
		if(!node) return path;
		node = node.getParent();
		if(!node) return path;
		return this.getFullPath(separator, field, node);
	},

	getFullPath: function(separator, field, node) {
		var path = '';
		node = node ? node : this.lastFocused;
		if(!node) return path;
		do {
			path = this.model.store.getValue(node.item, field) + (path == '' ? '' : separator + path);
			node = node.getParent();
		} while (node && !node.item.root);
		return path;
	}	

	
/*	expander: function (node) {
		if (node.declaredClass == 'dijit._TreeNode') {
			dojo.connect(node, 'addChild', this, 'expander');
		}
		var nodePath = this.getPath(node, '/');
		if ((this.path.substr(0, nodePath.length) == nodePath) && node.isFolder) {
			this._controller._expand(node);
		}
	},
	
	addChild: function () {
		dijit.Tree.prototype.addChild.apply(this, arguments);
		this.expander(arguments[0], 1);
	}*/
});