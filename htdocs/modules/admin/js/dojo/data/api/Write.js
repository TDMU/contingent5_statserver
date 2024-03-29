/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/data/api/Write",["../..","./Read"],function(a){a.declare("dojo.data.api.Write",a.data.api.Read,{getFeatures:function(){return{"dojo.data.api.Read":!0,"dojo.data.api.Write":!0}},newItem:function(){throw Error("Unimplemented API: dojo.data.api.Write.newItem");},deleteItem:function(){throw Error("Unimplemented API: dojo.data.api.Write.deleteItem");},setValue:function(){throw Error("Unimplemented API: dojo.data.api.Write.setValue");},setValues:function(){throw Error("Unimplemented API: dojo.data.api.Write.setValues");
},unsetAttribute:function(){throw Error("Unimplemented API: dojo.data.api.Write.clear");},save:function(){throw Error("Unimplemented API: dojo.data.api.Write.save");},revert:function(){throw Error("Unimplemented API: dojo.data.api.Write.revert");},isDirty:function(){throw Error("Unimplemented API: dojo.data.api.Write.isDirty");}});return a.data.api.Write});