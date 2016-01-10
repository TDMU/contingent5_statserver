//>>built
define("dojox/geo/openlayers/JsonImport",["dojo/_base/kernel","dojo/_base/declare","dojo/_base/xhr","dojo/_base/lang","dojo/_base/array","dojox/geo/openlayers/LineString","dojox/geo/openlayers/Collection","dojo/data/ItemFileReadStore","dojox/geo/openlayers/GeometryFeature"],function(v,q,r,c,s,t,u,w,o){return q("dojox.geo.openlayers.JsonImport",null,{constructor:function(b){this._params=b},loadData:function(){r.get({url:this._params.url,handleAs:"json",sync:!0,load:c.hitch(this,this._gotData),error:c.hitch(this,
this._loadError)})},_gotData:function(b){var d=this._params.nextFeature;if(c.isFunction(d)){var a=b.layerExtent,l=a[0],e=a[1],f=l+a[2],g=e+a[3],a=b.layerExtentLL,h=a[0],i=a[1],p=i+a[3],j=h+a[2],b=b.features,k;for(k in b){var a=b[k].shape,m=null;if(c.isArray(a[0])){var n=[];s.forEach(a,function(a){a=this._makeGeometry(a,l,e,f,g,h,p,j,i);n.push(a)},this);a=new u(n);m=new o(a)}else m=this._makeFeature(a,l,e,f,g,h,p,j,i);d.call(this,m)}d=this._params.complete;c.isFunction(d)&&d.call(this,d)}},_makeGeometry:function(b,
d,a,l,e,f,g,h,i){for(var c=[],j=0,k=0;k<b.length-1;k+=2){var m=b[k+1],j=(b[k]-d)/(l-d),n=j*(h-f)+f,j=(m-a)/(e-a);c.push({x:n,y:j*(i-g)+g})}return new t(c)},_makeFeature:function(b,d,a,c,e,f,g,h,i){b=this._makeGeometry(b,d,a,c,e,f,g,h,i);return new o(b)},_loadError:function(){var b=this._params.error;c.isFunction(b)&&b.apply(this,parameters)}})});