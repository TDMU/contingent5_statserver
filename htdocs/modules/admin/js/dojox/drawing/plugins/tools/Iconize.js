//>>built
define(["dijit","dojo","dojox","dojo/require!dojox/drawing/plugins/_Plugin"],function(j,f,b){f.provide("dojox.drawing.plugins.tools.Iconize");f.require("dojox.drawing.plugins._Plugin");b.drawing.plugins.tools.Iconize=b.drawing.util.oo.declare(b.drawing.plugins._Plugin,function(){},{onClick:function(){var c,e;for(e in this.stencils.stencils)if(console.log(" stanceil item:",this.stencils.stencils[e].id,this.stencils.stencils[e]),this.stencils.stencils[e].shortType=="path"){c=this.stencils.stencils[e];
break}c&&(console.log("click Iconize plugin",c.points),this.makeIcon(c.points))},makeIcon:function(c){var e=1E4,b=1E4;c.forEach(function(a){a.x!==void 0&&!isNaN(a.x)&&(e=Math.min(e,a.x),b=Math.min(b,a.y))});var g=0,h=0;c.forEach(function(a){if(a.x!==void 0&&!isNaN(a.x))a.x=Number((a.x-e).toFixed(1)),a.y=Number((a.y-b).toFixed(1)),g=Math.max(g,a.x),h=Math.max(h,a.y)});console.log("xmax:",g,"ymax:",h);c.forEach(function(a){a.x=Number((a.x/g).toFixed(1))*60+20;a.y=Number((a.y/h).toFixed(1))*60+20});
var d="[\n";f.forEach(c,function(a,b){d+="{\t";a.t&&(d+="t:'"+a.t+"'");a.x!==void 0&&!isNaN(a.x)&&(a.t&&(d+=", "),d+="x:"+a.x+",\t\ty:"+a.y);d+="\t}";b!=c.length-1&&(d+=",");d+="\n"});d+="]";console.log(d);var i=f.byId("data");if(i)i.value=d}});b.drawing.plugins.tools.Iconize.setup={name:"dojox.drawing.plugins.tools.Iconize",tooltip:"Iconize Tool",iconClass:"iconPan"};b.drawing.register(b.drawing.plugins.tools.Iconize.setup,"plugin")});