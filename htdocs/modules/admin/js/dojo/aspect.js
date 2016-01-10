/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

//>>built
define("dojo/aspect",[],function(){function j(g,a,e,h){var b=g[a],f=a=="around",c;if(f){var k=e(function(){return b.advice(this,arguments)});c={remove:function(){c.cancelled=!0},advice:function(d,a){return c.cancelled?b.advice(d,a):k.apply(d,a)}}}else c={remove:function(){var d=c.previous,b=c.next;if(!b&&!d)delete g[a];else if(d?d.next=b:g[a]=b,b)b.previous=d},advice:e,receiveArguments:h};if(b&&!f)if(a=="after"){for(e=b;e;)b=e,e=e.next;b.next=c;c.previous=b}else{if(a=="before")g[a]=c,c.next=b,b.previous=
c}else g[a]=c;return c}function i(g){return function(a,e,h,b){var f=a[e],c;if(!f||f.target!=a){c=a[e]=function(){for(var a=arguments,d=c.before;d;)a=d.advice.apply(this,a)||a,d=d.next;if(c.around)var b=c.around.advice(this,a);for(d=c.after;d;)b=d.receiveArguments?d.advice.apply(this,a)||b:d.advice.call(this,b),d=d.next;return b};if(f)c.around={advice:function(a,b){return f.apply(a,b)}};c.target=a}a=j(c||f,g,h,b);h=null;return a}}return{before:i("before"),around:i("around"),after:i("after")}});