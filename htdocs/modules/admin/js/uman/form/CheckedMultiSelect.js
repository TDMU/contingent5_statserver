require({cache:{
'url:uman/form/resources/CheckedMultiSelect.html':"<div class=\"dijit dijitReset dijitInline dijitLeft\" id=\"widget_${id}\"\n\t><div data-dojo-attach-point=\"comboButtonNode\"\n\t></div\n\t><div data-dojo-attach-point=\"selectNode\" class=\"dijit dijitReset dijitInline ${baseClass}Wrapper\" data-dojo-attach-event=\"onmousedown:_onMouseDown,onclick:focus\"\n\t\t><select class=\"${baseClass}Select dojoxCheckedMultiSelectHidden\" multiple=\"true\" data-dojo-attach-point=\"containerNode,focusNode\" ${!nameAttrSetting}></select\n\t\t><div data-dojo-attach-point=\"wrapperDiv\"></div\n\t></div\n></div>"}});
define("uman/form/CheckedMultiSelect", [
	"dojo/_base/declare",
	"dojo/_base/lang",
	"dojo/_base/array",
	"dijit/_Widget",
	"dijit/_TemplatedMixin",
	"dijit/_WidgetsInTemplateMixin",
	"dojox/form/CheckedMultiSelect",
	"dojo/text!uman/form/resources/CheckedMultiSelect.html"
], function(declare, lang, array, Widget, TemplatedMixin, WidgetsInTemplateMixin, FormCheckedMultiSelect, CheckedMultiSelect){
	
	var formCheckedMultiSelect = declare("uman.form.CheckedMultiSelect", FormCheckedMultiSelect, {
		templateString: CheckedMultiSelect,
		
		// Переопределение метода из dijit.form._FormSelectWidget
		_setValueAttr: function(/*anything*/ newValue, /*Boolean?*/ priorityChange){
			// summary:
			//		set the value of the widget.
			//		If a string is passed, then we set our value from looking it up.
			if(this._loadingStore){
				// Our store is loading - so save our value, and we'll set it when
				// we're done
				this._pendingValue = newValue;
				return;
			}
			var opts = this.getOptions() || [];
			if(!lang.isArray(newValue)){
				if(typeof newValue === "string"){		// Добавлено только это для установки значений 
					newValue = newValue.split(',');		// через атрибут value
				} else newValue = [newValue];
			}
			array.forEach(newValue, function(i, idx){
				if(!lang.isObject(i)){
					i = i + "";
				}
				if(typeof i === "string"){
					newValue[idx] = array.filter(opts, function(node){
						return node.value === i;
					})[0] || {value: "", label: ""};
				}
			}, this);

			// Make sure some sane default is set
			newValue = array.filter(newValue, function(i){ return i && i.value; });
			if(!this.multiple && (!newValue[0] || !newValue[0].value) && opts.length){
				newValue[0] = opts[0];
			}
			array.forEach(opts, function(i){
				i.selected = array.some(newValue, function(v){ return v.value === i.value; });
			});
			var val = array.map(newValue, function(i){ return i.value; }),
				disp = array.map(newValue, function(i){ return i.label; });

			this._set("value", this.multiple ? val : val[0]);
			this._setDisplay(this.multiple ? disp : disp[0]);
			this._updateSelection();
			this._handleOnChange(this.value, priorityChange);
		},
	
	_updateSelection: function(){
		this.inherited(arguments);
/*		this._handleOnChange(this.value);
		array.forEach(this._getChildren(), function(item){
			item._updateBox();
		});*/
// -----------------------------------------------------	
//		console.debug(this.value);
		
		dojo.empty(this.containerNode);
		_this = this;
		dojo.forEach(this.value, function(item) {
			var opt = dojo.create('option', {
				'value': item,
				'label': item,
				'selected': 'selected'
			});
			opt.innerHTML = item;
			dojo.place(opt, _this.containerNode);
		});
// -----------------------------------------------------		
		
/*		if(this.dropDown && this.dropDownButton){
			var i = 0, label = "";
			array.forEach(this.options, function(option){
				if(option.selected){
					i++;
					label = option.label;
				}
			});
			this.dropDownButton.set("label", this.multiple ?
				lang.replace(this._nlsResources.multiSelectLabelText, {num: i}) :
				label);
		}*/
	}}
);
	return formCheckedMultiSelect;	
});