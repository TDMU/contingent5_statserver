dojo.provide("uman.ForestStoreModel");

dojo.require("dijit.tree.ForestStoreModel");

dojo.declare("uman.ForestStoreModel", dijit.tree.ForestStoreModel, {
	getChildren: function(item, complete_cb, error_cb) {
		if (item.root == true) pid = -1;
		else pid = this.store.getIdentity(item);
		this.store.fetch({ query: {id: pid}, onComplete: complete_cb, onError: error_cb});
		return this.inherited(arguments);
	},
	mayHaveChildren: function(item) {
		return (item.root == true) || (this.store.getValue(item, "HAS_CHILDS") == 1);
	}
});