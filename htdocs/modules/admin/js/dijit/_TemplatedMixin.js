//>>built
define("dijit/_TemplatedMixin",["dojo/_base/lang","dojo/touch","./_WidgetBase","dojo/string","dojo/cache","dojo/_base/array","dojo/_base/declare","dojo/dom-construct","dojo/_base/sniff","dojo/_base/unload","dojo/_base/window"],function(i,m,n,k,o,l,p,g,q,r,s){var f=p("dijit._TemplatedMixin",null,{templateString:null,templatePath:null,_skipNodeCache:!1,_earlyTemplatedStartup:!1,constructor:function(){this._attachPoints=[];this._attachEvents=[]},_stringRepl:function(a){var b=this.declaredClass,e=this;
return k.substitute(a,this,function(a,c){c.charAt(0)=="!"&&(a=i.getObject(c.substr(1),!1,e));if(typeof a=="undefined")throw Error(b+" template:"+c);if(a==null)return"";return c.charAt(0)=="!"?a:a.toString().replace(/"/g,"&quot;")},this)},buildRendering:function(){if(!this.templateString)this.templateString=o(this.templatePath,{sanitize:!0});var a=f.getCachedTemplate(this.templateString,this._skipNodeCache),b;if(i.isString(a)){if(b=g.toDom(this._stringRepl(a)),b.nodeType!=1)throw Error("Invalid template: "+
a);}else b=a.cloneNode(!0);this.domNode=b;this.inherited(arguments);this._attachTemplateNodes(b,function(a,b){return a.getAttribute(b)});this._beforeFillContent();this._fillContent(this.srcNodeRef)},_beforeFillContent:function(){},_fillContent:function(a){var b=this.containerNode;if(a&&b)for(;a.hasChildNodes();)b.appendChild(a.firstChild)},_attachTemplateNodes:function(a,b){for(var e=i.isArray(a)?a:a.all||a.getElementsByTagName("*"),h=i.isArray(a)?0:-1;h<e.length;h++){var c=h==-1?a:e[h];if(!this.widgetsInTemplate||
!b(c,"dojoType")&&!b(c,"data-dojo-type")){var d=b(c,"dojoAttachPoint")||b(c,"data-dojo-attach-point");if(d)for(var f=d.split(/\s*,\s*/);d=f.shift();)i.isArray(this[d])?this[d].push(c):this[d]=c,this._attachPoints.push(d);if(d=b(c,"dojoAttachEvent")||b(c,"data-dojo-attach-event"))for(var f=d.split(/\s*,\s*/),g=i.trim;d=f.shift();)if(d){var j=null;d.indexOf(":")!=-1?(j=d.split(":"),d=g(j[0]),j=g(j[1])):d=g(d);j||(j=d);this._attachEvents.push(this.connect(c,m[d]||d,j))}}}},destroyRendering:function(){l.forEach(this._attachPoints,
function(a){delete this[a]},this);this._attachPoints=[];l.forEach(this._attachEvents,this.disconnect,this);this._attachEvents=[];this.inherited(arguments)}});f._templateCache={};f.getCachedTemplate=function(a,b){var e=f._templateCache,h=a,c=e[h];if(c){try{if(!c.ownerDocument||c.ownerDocument==s.doc)return c}catch(d){}g.destroy(c)}a=k.trim(a);if(b||a.match(/\$\{([^\}]+)\}/g))return e[h]=a;else{c=g.toDom(a);if(c.nodeType!=1)throw Error("Invalid template: "+a);return e[h]=c}};q("ie")&&r.addOnWindowUnload(function(){var a=
f._templateCache,b;for(b in a){var e=a[b];typeof e=="object"&&g.destroy(e);delete a[b]}});i.extend(n,{dojoAttachEvent:"",dojoAttachPoint:""});return f});