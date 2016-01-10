//>>built
define(["dijit","dojo","dojox"],function(t,m,q){m.provide("dojox.jsonPath.query");q.jsonPath.query=function(l,n,g){g||(g={});var r=[];if(g.resultType=="PATH"&&g.evalType=="RESULT")throw Error("RESULT based evaluation not supported with PATH based results");var d={resultType:g.resultType||"VALUE",normalize:function(a){for(var c=[],a=a.replace(/'([^']|'')*'/g,function(a){return"_str("+(r.push(eval(a))-1)+")"}),b=-1;b!=c.length;)b=c.length,a=a.replace(/(\??\([^\(\)]*\))/g,function(a){return"#"+(c.push(a)-
1)});a=a.replace(/[\['](#[0-9]+)[\]']/g,"[$1]").replace(/'?\.'?|\['?/g,";").replace(/;;;|;;/g,";..;").replace(/;$|'?\]|'$/g,"");for(b=-1;b!=a;)b=a,a=a.replace(/#([0-9]+)/g,function(a,b){return c[b]});return a.split(";")},asPaths:function(a){for(var c=0;c<a.length;c++){for(var b="$",d=a[c],o=1,g=d.length;o<g;o++)b+=/^[0-9*]+$/.test(d[o])?"["+d[o]+"]":"['"+d[o]+"']";a[c]=b}return a},exec:function(a,c,b){function i(a,b,c){a&&a.hasOwnProperty(b)&&d.resultType!="VALUE"&&f.push(h.concat([b]));c?j=a[b]:
a&&a.hasOwnProperty(b)&&j.push(a[b])}function g(a){j.push(a);f.push(h);d.walk(a,function(b){if(typeof a[b]==="object"){var c=h;h=h.concat(b);g(a[b]);h=c}})}function m(a,b){if(b instanceof Array){var c=b.length,d=0,k=c,e=1;a.replace(/^(-?[0-9]*):(-?[0-9]*):?(-?[0-9]*)$/g,function(a,b,c,s){d=parseInt(b||d);k=parseInt(c||k);e=parseInt(s||e)});d=d<0?Math.max(0,d+c):Math.min(c,d);k=k<0?Math.max(0,k+c):Math.min(c,k);for(c=d;c<k;c+=e)i(b,c)}}function l(a){var b=e.match(/^_str\(([0-9]+)\)$/);return b?r[b[1]]:
a}function n(a){if(/^\(.*?\)$/.test(e))i(a,d.eval(e,a),b);else if(e==="*")d.walk(a,b&&a instanceof Array?function(b){d.walk(a[b],function(c){i(a[b],c)})}:function(b){i(a,b)});else if(e==="..")g(a);else if(/,/.test(e))for(var c=e.split(/'?,'?/),f=0,h=c.length;f<h;f++)i(a,l(c[f]));else/^\?\(.*?\)$/.test(e)?d.walk(a,function(b){d.eval(e.replace(/^\?\((.*?)\)$/,"$1"),a[b])&&i(a,b)}):/^(-?[0-9]*):(-?[0-9]*):?([0-9]*)$/.test(e)?m(e,a):(e=l(e),b&&a instanceof Array&&!/^[0-9*]+$/.test(e)?d.walk(a,function(b){i(a[b],
e)}):i(a,e,b))}for(var h=["$"],j=b?c:[c],f=[h];a.length;){var e=a.shift();if((c=j)===null||c===void 0)return c;var j=[],q=f,f=[];b?n(c):d.walk(c,function(a){h=q[a]||h;n(c[a])})}if(d.resultType=="BOTH"){for(var f=d.asPaths(f),a=[],p=0;p<f.length;p++)a.push({path:f[p],value:j[p]});return a}return d.resultType=="PATH"?d.asPaths(f):j},walk:function(a,c){if(a instanceof Array)for(var b=0,d=a.length;b<d;b++)b in a&&c(b);else if(typeof a==="object")for(b in a)a.hasOwnProperty(b)&&c(b)},eval:function(a,c){try{return m&&
c&&eval(a.replace(/@/g,"_v"))}catch(b){throw new SyntaxError("jsonPath: "+b.message+": "+a.replace(/@/g,"_v").replace(/\^/g,"_a"));}}},m=l;if(n&&l)return d.exec(d.normalize(n).slice(1),l,g.evalType=="RESULT");return!1}});