/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/dnd/common",["../main"],function(a){a.getObject("dnd",!0,a);a.dnd.getCopyKeyState=a.isCopyKey;a.dnd._uniqueId=0;a.dnd.getUniqueId=function(){var b;do b=a._scopeName+"Unique"+ ++a.dnd._uniqueId;while(a.byId(b));return b};a.dnd._empty={};a.dnd.isFormElement=function(a){a=a.target;if(a.nodeType==3)a=a.parentNode;return" button textarea input select option ".indexOf(" "+a.tagName.toLowerCase()+" ")>=0};return a.dnd});