//>>built
define(["dijit","dojo","dojox","dojo/require!dojox/drawing/tools/TextBlock"],function(c,b,a){b.provide("dojox.drawing.tools.custom.Equation");b.require("dojox.drawing.tools.TextBlock");a.drawing.tools.custom.Equation=a.drawing.util.oo.declare(a.drawing.tools.TextBlock,function(){},{customType:"equation"});a.drawing.tools.custom.Equation.setup={name:"dojox.drawing.tools.custom.Equation",tooltip:"Equation Tool",iconClass:"iconEq"};a.drawing.register(a.drawing.tools.custom.Equation.setup,"tool")});