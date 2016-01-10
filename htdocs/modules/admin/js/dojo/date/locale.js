/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/date/locale",["../_base/kernel","../_base/lang","../_base/array","../date","../cldr/supplemental","../regexp","../string","../i18n!../cldr/nls/gregorian"],function(h,k,v,o,r,s,p){function t(a,e,c,d){return d.replace(/([a-z])\1*/ig,function(i){var b,g,f=i.charAt(0),i=i.length,l=["abbr","wide","narrow"];switch(f){case "G":b=e[i<4?"eraAbbr":"eraNames"][a.getFullYear()<0?0:1];break;case "y":b=a.getFullYear();switch(i){case 1:break;case 2:if(!c.fullYear){b=String(b);b=b.substr(b.length-2);
break}default:g=!0}break;case "Q":case "q":b=Math.ceil((a.getMonth()+1)/3);g=!0;break;case "M":b=a.getMonth();i<3?(b+=1,g=!0):(f=["months-format",l[i-3]].join("-"),b=e[f][b]);break;case "w":b=h.date.locale._getWeekOfYear(a,0);g=!0;break;case "d":b=a.getDate();g=!0;break;case "D":b=h.date.locale._getDayOfYear(a);g=!0;break;case "E":b=a.getDay();i<3?(b+=1,g=!0):(f=["days-format",l[i-3]].join("-"),b=e[f][b]);break;case "a":b=a.getHours()<12?"am":"pm";b=c[b]||e["dayPeriods-format-wide-"+b];break;case "h":case "H":case "K":case "k":g=
a.getHours();switch(f){case "h":b=g%12||12;break;case "H":b=g;break;case "K":b=g%12;break;case "k":b=g||24}g=!0;break;case "m":b=a.getMinutes();g=!0;break;case "s":b=a.getSeconds();g=!0;break;case "S":b=Math.round(a.getMilliseconds()*Math.pow(10,i-3));g=!0;break;case "v":case "z":if(b=h.date.locale._getZone(a,!0,c))break;i=4;case "Z":b=h.date.locale._getZone(a,!1,c);b=[b<=0?"+":"-",p.pad(Math.floor(Math.abs(b)/60),2),p.pad(Math.abs(b)%60,2)];i==4&&(b.splice(0,0,"GMT"),b.splice(3,0,":"));b=b.join("");
break;default:throw Error("dojo.date.locale.format: invalid pattern char: "+d);}g&&(b=p.pad(b,i));return b})}function n(a,e,c,d){var i=function(b){return b},e=e||i,c=c||i,d=d||i,b=a.match(/(''|[^'])+/g),g=a.charAt(0)=="'";h.forEach(b,function(a,d){a?(b[d]=(g?c:e)(a.replace(/''/g,"'")),g=!g):b[d]=""});return d(b.join(""))}function u(a,e,c,d){d=s.escapeString(d);c.strict||(d=d.replace(" a"," ?a"));return d.replace(/([a-z])\1*/ig,function(d){var b;b=d.charAt(0);var g=d.length,f="",h="";c.strict?(g>1&&
(f="0{"+(g-1)+"}"),g>2&&(h="0{"+(g-2)+"}")):(f="0?",h="0{0,2}");switch(b){case "y":b="\\d{2,4}";break;case "M":b=g>2?"\\S+?":"1[0-2]|"+f+"[1-9]";break;case "D":b="[12][0-9][0-9]|3[0-5][0-9]|36[0-6]|"+f+"[1-9][0-9]|"+h+"[1-9]";break;case "d":b="3[01]|[12]\\d|"+f+"[1-9]";break;case "w":b="[1-4][0-9]|5[0-3]|"+f+"[1-9]";break;case "E":b="\\S+";break;case "h":b="1[0-2]|"+f+"[1-9]";break;case "k":b="1[01]|"+f+"\\d";break;case "H":b="1\\d|2[0-3]|"+f+"\\d";break;case "K":b="1\\d|2[0-4]|"+f+"[1-9]";break;
case "m":case "s":b="[0-5]\\d";break;case "S":b="\\d{"+g+"}";break;case "a":g=c.am||e["dayPeriods-format-wide-am"];f=c.pm||e["dayPeriods-format-wide-pm"];b=g+"|"+f;c.strict||(g!=g.toLowerCase()&&(b+="|"+g.toLowerCase()),f!=f.toLowerCase()&&(b+="|"+f.toLowerCase()),b.indexOf(".")!=-1&&(b+="|"+b.replace(/\./g,"")));b=b.replace(/\./g,"\\.");break;default:b=".*"}a&&a.push(d);return"("+b+")"}).replace(/[\xa0 ]/g,"[\\s\\xa0]")}k.getObject("date.locale",!0,h);h.date.locale._getZone=function(a,e){return e?
o.getTimezoneName(a):a.getTimezoneOffset()};h.date.locale.format=function(a,e){var e=e||{},c=h.i18n.normalizeLocale(e.locale),d=e.formatLength||"short",c=h.date.locale._getGregorianBundle(c),i=[],b=k.hitch(this,t,a,c,e);if(e.selector=="year")return n(c["dateFormatItem-yyyy"]||"yyyy",b);var g;e.selector!="date"&&(g=e.timePattern||c["timeFormat-"+d])&&i.push(n(g,b));e.selector!="time"&&(g=e.datePattern||c["dateFormat-"+d])&&i.push(n(g,b));return i.length==1?i[0]:c["dateTimeFormat-"+d].replace(/\{(\d+)\}/g,
function(b,a){return i[a]})};h.date.locale.regexp=function(a){return h.date.locale._parseInfo(a).regexp};h.date.locale._parseInfo=function(a){var a=a||{},e=h.i18n.normalizeLocale(a.locale),e=h.date.locale._getGregorianBundle(e),c=a.formatLength||"short",d=a.datePattern||e["dateFormat-"+c],i=a.timePattern||e["timeFormat-"+c],c=a.selector=="date"?d:a.selector=="time"?i:e["dateTimeFormat-"+c].replace(/\{(\d+)\}/g,function(b,a){return[i,d][a]}),b=[];return{regexp:n(c,k.hitch(this,u,b,e,a)),tokens:b,bundle:e}};
h.date.locale.parse=function(a,e){var c=/[\u200E\u200F\u202A\u202E]/g,d=h.date.locale._parseInfo(e),i=d.tokens,b=d.bundle,c=RegExp("^"+d.regexp.replace(c,"")+"$",d.strict?"":"i").exec(a&&a.replace(c,""));if(!c)return null;var g=["abbr","wide","narrow"],f=[1970,0,1,0,0,0,0],l="",c=h.every(c,function(a,d){if(!d)return!0;var c=i[d-1],j=c.length;switch(c.charAt(0)){case "y":if(j!=2&&e.strict)f[0]=a;else if(a<100)a=Number(a),j=""+(new Date).getFullYear(),c=j.substring(0,2)*100,j=Math.min(Number(j.substring(2,
4))+20,99),f[0]=a<j?c+a:c-100+a;else{if(e.strict)return!1;f[0]=a}break;case "M":if(j>2){if(c=b["months-format-"+g[j-3]].concat(),e.strict||(a=a.replace(".","").toLowerCase(),c=h.map(c,function(a){return a.replace(".","").toLowerCase()})),a=h.indexOf(c,a),a==-1)return!1}else a--;f[1]=a;break;case "E":case "e":c=b["days-format-"+g[j-3]].concat();e.strict||(a=a.toLowerCase(),c=h.map(c,function(a){return a.toLowerCase()}));a=h.indexOf(c,a);if(a==-1)return!1;break;case "D":f[1]=0;case "d":f[2]=a;break;
case "a":c=e.am||b["dayPeriods-format-wide-am"];j=e.pm||b["dayPeriods-format-wide-pm"];if(!e.strict)var k=/\./g,a=a.replace(k,"").toLowerCase(),c=c.replace(k,"").toLowerCase(),j=j.replace(k,"").toLowerCase();if(e.strict&&a!=c&&a!=j)return!1;l=a==j?"p":a==c?"a":"";break;case "K":a==24&&(a=0);case "h":case "H":case "k":if(a>23)return!1;f[3]=a;break;case "m":f[4]=a;break;case "s":f[5]=a;break;case "S":f[6]=a}return!0}),d=+f[3];l==="p"&&d<12?f[3]=d+12:l==="a"&&d==12&&(f[3]=0);d=new Date(f[0],f[1],f[2],
f[3],f[4],f[5],f[6]);e.strict&&d.setFullYear(f[0]);var m=i.join(""),k=m.indexOf("d")!=-1,m=m.indexOf("M")!=-1;if(!c||m&&d.getMonth()>f[1]||k&&d.getDate()>f[2])return null;if(m&&d.getMonth()<f[1]||k&&d.getDate()<f[2])d=o.add(d,"hour",1);return d};var q=[];h.date.locale.addCustomFormats=function(a,e){q.push({pkg:a,name:e})};h.date.locale._getGregorianBundle=function(a){var e={};h.forEach(q,function(c){c=h.i18n.getLocalization(c.pkg,c.name,a);e=k.mixin(e,c)},this);return e};h.date.locale.addCustomFormats("dojo.cldr",
"gregorian");h.date.locale.getNames=function(a,e,c,d){var i,d=h.date.locale._getGregorianBundle(d),a=[a,c,e];c=="standAlone"&&(c=a.join("-"),i=d[c],i[0]==1&&(i=void 0));a[1]="format";return(i||d[a.join("-")]).concat()};h.date.locale.isWeekend=function(a,e){var c=r.getWeekend(e),d=(a||new Date).getDay();c.end<c.start&&(c.end+=7,d<c.start&&(d+=7));return d>=c.start&&d<=c.end};h.date.locale._getDayOfYear=function(a){return o.difference(new Date(a.getFullYear(),0,1,a.getHours()),a)+1};h.date.locale._getWeekOfYear=
function(a,e){arguments.length==1&&(e=0);var c=(new Date(a.getFullYear(),0,1)).getDay(),d=Math.floor((h.date.locale._getDayOfYear(a)+(c-e+7)%7-1)/7);c==e&&d++;return d};return h.date.locale});