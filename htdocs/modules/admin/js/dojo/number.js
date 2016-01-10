/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/number",["./_base/kernel","./_base/lang","./i18n","./i18n!./cldr/nls/number","./string","./regexp"],function(f,m,k,p,n,i){m.getObject("number",!0,f);f.number.format=function(b,a){var a=m.mixin({},a||{}),c=k.normalizeLocale(a.locale),c=k.getLocalization("dojo.cldr","number",c);a.customs=c;c=a.pattern||c[(a.type||"decimal")+"Format"];if(isNaN(b)||Math.abs(b)==Infinity)return null;return f.number._applyPattern(b,c,a)};f.number._numberPatternRE=/[#0,]*[#0](?:\.0*#*)?/;f.number._applyPattern=
function(b,a,c){var c=c||{},e=c.customs.group,d=c.customs.decimal,a=a.split(";"),g=a[0],a=a[b<0?1:0]||"-"+g;if(a.indexOf("%")!=-1)b*=100;else if(a.indexOf("\u2030")!=-1)b*=1E3;else if(a.indexOf("\u00a4")!=-1)e=c.customs.currencyGroup||e,d=c.customs.currencyDecimal||d,a=a.replace(/\u00a4{1,3}/,function(b){return c[["symbol","currency","displayName"][b.length-1]]||c.currency||""});else if(a.indexOf("E")!=-1)throw Error("exponential notation not supported");var h=f.number._numberPatternRE,g=g.match(h);
if(!g)throw Error("unable to find a number expression in pattern: "+a);if(c.fractional===!1)c.places=0;return a.replace(h,f.number._formatAbsolute(b,g[0],{decimal:d,group:e,places:c.places,round:c.round}))};f.number.round=function(b,a,c){c=10/(c||10);return(c*+b).toFixed(a)/c};if((0.9).toFixed()==0){var o=f.number.round;f.number.round=function(b,a,c){var e=Math.pow(10,-a||0),d=Math.abs(b);if(!b||d>=e||d*Math.pow(10,a+1)<5)e=0;return o(b,a,c)+(b>0?e:-e)}}f.number._formatAbsolute=function(b,a,c){c=
c||{};if(c.places===!0)c.places=0;if(c.places===Infinity)c.places=6;var a=a.split("."),e=typeof c.places=="string"&&c.places.indexOf(","),d=c.places;if(e)d=c.places.substring(e+1);else if(!(d>=0))d=(a[1]||[]).length;c.round<0||(b=f.number.round(b,d,c.round));var b=String(Math.abs(b)).split("."),g=b[1]||"";if(a[1]||c.places){if(e)c.places=c.places.substring(0,e);e=c.places!==void 0?c.places:a[1]&&a[1].lastIndexOf("0")+1;e>g.length&&(b[1]=n.pad(g,e,"0",!0));d<g.length&&(b[1]=g.substr(0,d))}else b[1]&&
b.pop();d=a[0].replace(",","");e=d.indexOf("0");e!=-1&&(e=d.length-e,e>b[0].length&&(b[0]=n.pad(b[0],e)),d.indexOf("#")==-1&&(b[0]=b[0].substr(b[0].length-e)));var d=a[0].lastIndexOf(","),h,l;d!=-1&&(h=a[0].length-d-1,a=a[0].substr(0,d),d=a.lastIndexOf(","),d!=-1&&(l=a.length-d-1));a=[];for(d=b[0];d;)e=d.length-h,a.push(e>0?d.substr(e):d),d=e>0?d.slice(0,e):"",l&&(h=l,delete l);b[0]=a.reverse().join(c.group||",");return b.join(c.decimal||".")};f.number.regexp=function(b){return f.number._parseInfo(b).regexp};
f.number._parseInfo=function(b){var b=b||{},a=k.normalizeLocale(b.locale),a=k.getLocalization("dojo.cldr","number",a),c=b.pattern||a[(b.type||"decimal")+"Format"],e=a.group,d=a.decimal,g=1;if(c.indexOf("%")!=-1)g/=100;else if(c.indexOf("\u2030")!=-1)g/=1E3;else{var h=c.indexOf("\u00a4")!=-1;h&&(e=a.currencyGroup||e,d=a.currencyDecimal||d)}a=c.split(";");a.length==1&&a.push("-"+a[0]);a=i.buildGroupRE(a,function(a){a="(?:"+i.escapeString(a,".")+")";return a.replace(f.number._numberPatternRE,function(a){var c=
{signed:!1,separator:b.strict?e:[e,""],fractional:b.fractional,decimal:d,exponent:!1},a=a.split("."),j=b.places;a.length==1&&g!=1&&(a[1]="###");if(a.length==1||j===0)c.fractional=!1;else{j===void 0&&(j=b.pattern?a[1].lastIndexOf("0")+1:Infinity);if(j&&b.fractional==void 0)c.fractional=!0;!b.places&&j<a[1].length&&(j+=","+a[1].length);c.places=j}a=a[0].split(",");if(a.length>1&&(c.groupSize=a.pop().length,a.length>1))c.groupSize2=a.pop().length;return"("+f.number._realNumberRegexp(c)+")"})},!0);h&&
(a=a.replace(/([\s\xa0]*)(\u00a4{1,3})([\s\xa0]*)/g,function(a,c,e,d){a=i.escapeString(b[["symbol","currency","displayName"][e.length-1]]||b.currency||"");c=c?"[\\s\\xa0]":"";d=d?"[\\s\\xa0]":"";if(!b.strict)return c&&(c+="*"),d&&(d+="*"),"(?:"+c+a+d+")?";return c+a+d}));return{regexp:a.replace(/[\xa0 ]/g,"[\\s\\xa0]"),group:e,decimal:d,factor:g}};f.number.parse=function(b,a){var c=f.number._parseInfo(a),e=RegExp("^"+c.regexp+"$").exec(b);if(!e)return NaN;var d=e[1];if(!e[1]){if(!e[2])return NaN;
d=e[2];c.factor*=-1}d=d.replace(RegExp("["+c.group+"\\s\\xa0]","g"),"").replace(c.decimal,".");return d*c.factor};f.number._realNumberRegexp=function(b){b=b||{};if(!("places"in b))b.places=Infinity;if(typeof b.decimal!="string")b.decimal=".";if(!("fractional"in b)||/^0/.test(b.places))b.fractional=[!0,!1];if(!("exponent"in b))b.exponent=[!0,!1];if(!("eSigned"in b))b.eSigned=[!0,!1];var a=f.number._integerRegexp(b),c=i.buildGroupRE(b.fractional,function(a){var c="";a&&b.places!==0&&(c="\\"+b.decimal,
b.places==Infinity?c="(?:"+c+"\\d+)?":c+="\\d{"+b.places+"}");return c},!0),e=i.buildGroupRE(b.exponent,function(a){if(a)return"([eE]"+f.number._integerRegexp({signed:b.eSigned})+")";return""});a+=c;c&&(a="(?:(?:"+a+")|(?:"+c+"))");return a+e};f.number._integerRegexp=function(b){b=b||{};if(!("signed"in b))b.signed=[!0,!1];if("separator"in b){if(!("groupSize"in b))b.groupSize=3}else b.separator="";var a=i.buildGroupRE(b.signed,function(a){return a?"[-+]":""},!0),c=i.buildGroupRE(b.separator,function(a){if(!a)return"(?:\\d+)";
a=i.escapeString(a);a==" "?a="\\s":a=="\u00a0"&&(a="\\s\\xa0");var c=b.groupSize,f=b.groupSize2;if(f)return a="(?:0|[1-9]\\d{0,"+(f-1)+"}(?:["+a+"]\\d{"+f+"})*["+a+"]\\d{"+c+"})",c-f>0?"(?:"+a+"|(?:0|[1-9]\\d{0,"+(c-1)+"}))":a;return"(?:0|[1-9]\\d{0,"+(c-1)+"}(?:["+a+"]\\d{"+c+"})*)"},!0);return a+c};return f.number});