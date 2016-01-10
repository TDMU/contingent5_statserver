//>>built
define(["dijit","dojo","dojox"],function(c,e,g){e.provide("dojox.lang.observable");e.experimental("dojox.lang.observable");g.lang.observable=function(c,e,h,i){return g.lang.makeObservable(e,h,i)(c)};g.lang.makeObservable=function(c,e,h,i){function m(b,d,a){return function(){return h(b,d,a,arguments)}}i=i||{};h=h||function(b,d,a,j){return d[a].apply(b,j)};if(g.lang.lettableWin){var k=g.lang.makeObservable;k.inc=(k.inc||0)+1;var n="gettable_"+k.inc;g.lang.lettableWin[n]=c;var o="settable_"+k.inc;g.lang.lettableWin[o]=
e;var p={};return function(b){if(b.__observable)return b.__observable;if(b.data__)throw Error("Can wrap an object that is already wrapped");var d=[],a,j;for(a in i)d.push(a);j={type:1,event:1};for(a in b)a.match(/^[a-zA-Z][\w\$_]*$/)&&!(a in i)&&!(a in j)&&d.push(a);var c=d.join(","),f;a=p[c];if(!a){var e="dj_lettable_"+k.inc++,h=e+"_dj_getter",l=["Class "+e,"\tPublic data__"];a=0;for(j=d.length;a<j;a++){f=d[a];var q=typeof b[f];q=="function"||i[f]?l.push("  Public "+f):q!="object"&&l.push("\tPublic Property Let "+
f+"(val)","\t\tCall "+o+'(me.data__,"'+f+'",val)',"\tEnd Property","\tPublic Property Get "+f,"\t\t"+f+" = "+n+'(me.data__,"'+f+'")',"\tEnd Property")}l.push("End Class");l.push("Function "+h+"()","\tDim tmp","\tSet tmp = New "+e,"\tSet "+h+" = tmp","End Function");g.lang.lettableWin.vbEval(l.join("\n"));p[c]=a=function(){return g.lang.lettableWin.construct(h)}}console.log("starting5");c=a();c.data__=b;console.log("starting6");try{b.__observable=c}catch(t){}a=0;for(j=d.length;a<j;a++){f=d[a];try{var r=
b[f]}catch(s){console.log("error ",f,s)}if(typeof r=="function"||i[f])c[f]=m(c,b,f)}return c}}else return function(b){if(b.__observable)return b.__observable;var d=b instanceof Array?[]:{};d.data__=b;for(var a in b)a.charAt(0)!="_"&&(typeof b[a]=="function"?d[a]=m(d,b,a):typeof b[a]!="object"&&function(a){d.__defineGetter__(a,function(){return c(b,a)});d.__defineSetter__(a,function(c){return e(b,a,c)})}(a));for(a in i)d[a]=m(d,b,a);return b.__observable=d}};if(!{}.__defineGetter__)if(e.isIE)document.body?
(c=document.createElement("iframe"),document.body.appendChild(c)):(document.write("<iframe id='dj_vb_eval_frame'></iframe>"),c=document.getElementById("dj_vb_eval_frame")),c.style.display="none",e=c.contentWindow.document,g.lang.lettableWin=c.contentWindow,e.write('<html><head><script language="VBScript" type="text/VBScript">Function vb_global_eval(code)ExecuteGlobal(code)End Function<\/script><script type="text/javascript">function vbEval(code){ \nreturn vb_global_eval(code);}function construct(name){ \nreturn window[name]();}<\/script></head><body>vb-eval</body></html>'),
e.close();else throw Error("This browser does not support getters and setters");g.lang.ReadOnlyProxy=g.lang.makeObservable(function(c,e){return c[e]},function(){})});