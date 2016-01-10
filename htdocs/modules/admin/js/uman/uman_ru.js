/*
	Copyright (c) 2004-2011, The Dojo Foundation All Rights Reserved.
	Available via Academic Free License >= 2.1 OR the modified BSD license.
	see: http://dojotoolkit.org/license for details
*/

/*
	This is an optimized version of Dojo, built for deployment and not for
	development. To get sources and documentation, please visit:

		http://dojotoolkit.org
*/

//>>built
require({cache:{
'dijit/nls/ru/common':function(){
define(
//begin v1.x content
({
	buttonOk: "ОК",
	buttonCancel: "Отмена",
	buttonSave: "Сохранить",
	itemClose: "Закрыть"
})
//end v1.x content
);

},
'dijit/form/nls/ru/ComboBox':function(){
define(
//begin v1.x content
({
		previousMessage: "Предыдущие варианты",
		nextMessage: "Следующие варианты"
})
//end v1.x content
);

},
'dijit/form/nls/ru/validate':function(){
define(
//begin v1.x content
({
	invalidMessage: "Указано недопустимое значение.",
	missingMessage: "Это обязательное значение.",
	rangeMessage: "Это значение вне диапазона."
})
//end v1.x content
);

},
'dojo/cldr/nls/ru/gregorian':function(){
define(
//begin v1.x content
{
	"dateFormatItem-yM": "M.y",
	"field-dayperiod": "AM/PM",
	"field-minute": "Минута",
	"eraNames": [
		"до н.э.",
		"н.э."
	],
	"dateFormatItem-MMMEd": "ccc, d MMM",
	"field-day-relative+-1": "Вчера",
	"field-weekday": "День недели",
	"dateFormatItem-yQQQ": "y QQQ",
	"field-day-relative+-2": "Позавчера",
	"dateFormatItem-MMdd": "dd.MM",
	"days-standAlone-wide": [
		"Воскресенье",
		"Понедельник",
		"Вторник",
		"Среда",
		"Четверг",
		"Пятница",
		"Суббота"
	],
	"dateFormatItem-MMM": "LLL",
	"months-standAlone-narrow": [
		"Я",
		"Ф",
		"М",
		"А",
		"М",
		"И",
		"И",
		"А",
		"С",
		"О",
		"Н",
		"Д"
	],
	"field-era": "Эра",
	"field-hour": "Час",
	"quarters-standAlone-abbr": [
		"1-й кв.",
		"2-й кв.",
		"3-й кв.",
		"4-й кв."
	],
	"dateFormatItem-yyMMMEEEd": "EEE, d MMM yy",
	"dateFormatItem-y": "y",
	"timeFormat-full": "H:mm:ss zzzz",
	"dateFormatItem-yyyy": "y",
	"months-standAlone-abbr": [
		"янв.",
		"февр.",
		"март",
		"апр.",
		"май",
		"июнь",
		"июль",
		"авг.",
		"сент.",
		"окт.",
		"нояб.",
		"дек."
	],
	"dateFormatItem-Ed": "E, d",
	"dateFormatItem-yMMM": "LLL y",
	"field-day-relative+0": "Сегодня",
	"dateFormatItem-yyyyLLLL": "LLLL y",
	"field-day-relative+1": "Завтра",
	"days-standAlone-narrow": [
		"В",
		"П",
		"В",
		"С",
		"Ч",
		"П",
		"С"
	],
	"eraAbbr": [
		"до н.э.",
		"н.э."
	],
	"field-day-relative+2": "Послезавтра",
	"dateFormatItem-yyyyMM": "MM.yyyy",
	"dateFormatItem-yyyyMMMM": "LLLL y",
	"dateFormat-long": "d MMMM y 'г'.",
	"timeFormat-medium": "H:mm:ss",
	"field-zone": "Часовой пояс",
	"dateFormatItem-Hm": "H:mm",
	"dateFormat-medium": "dd.MM.yyyy",
	"dateFormatItem-yyMM": "MM.yy",
	"dateFormatItem-Hms": "H:mm:ss",
	"dateFormatItem-yyMMM": "LLL yy",
	"quarters-standAlone-wide": [
		"1-й квартал",
		"2-й квартал",
		"3-й квартал",
		"4-й квартал"
	],
	"dateFormatItem-ms": "mm:ss",
	"dateFormatItem-yyyyQQQQ": "QQQQ y 'г'.",
	"field-year": "Год",
	"months-standAlone-wide": [
		"Январь",
		"Февраль",
		"Март",
		"Апрель",
		"Май",
		"Июнь",
		"Июль",
		"Август",
		"Сентябрь",
		"Октябрь",
		"Ноябрь",
		"Декабрь"
	],
	"field-week": "Неделя",
	"dateFormatItem-MMMd": "d MMM",
	"dateFormatItem-yyQ": "Q yy",
	"timeFormat-long": "H:mm:ss z",
	"months-format-abbr": [
		"янв.",
		"февр.",
		"марта",
		"апр.",
		"мая",
		"июня",
		"июля",
		"авг.",
		"сент.",
		"окт.",
		"нояб.",
		"дек."
	],
	"timeFormat-short": "H:mm",
	"dateFormatItem-H": "H",
	"field-month": "Месяц",
	"quarters-format-abbr": [
		"1-й кв.",
		"2-й кв.",
		"3-й кв.",
		"4-й кв."
	],
	"days-format-abbr": [
		"вс",
		"пн",
		"вт",
		"ср",
		"чт",
		"пт",
		"сб"
	],
	"dateFormatItem-M": "L",
	"days-format-narrow": [
		"В",
		"П",
		"В",
		"С",
		"Ч",
		"П",
		"С"
	],
	"field-second": "Секунда",
	"field-day": "День",
	"dateFormatItem-MEd": "E, d.M",
	"months-format-narrow": [
		"Я",
		"Ф",
		"М",
		"А",
		"М",
		"И",
		"И",
		"А",
		"С",
		"О",
		"Н",
		"Д"
	],
	"days-standAlone-abbr": [
		"Вс",
		"Пн",
		"Вт",
		"Ср",
		"Чт",
		"Пт",
		"Сб"
	],
	"dateFormat-short": "dd.MM.yy",
	"dateFormatItem-yMMMEd": "E, d MMM y",
	"dateFormat-full": "EEEE, d MMMM y 'г'.",
	"dateFormatItem-Md": "d.M",
	"dateFormatItem-yMEd": "EEE, d.M.y",
	"months-format-wide": [
		"января",
		"февраля",
		"марта",
		"апреля",
		"мая",
		"июня",
		"июля",
		"августа",
		"сентября",
		"октября",
		"ноября",
		"декабря"
	],
	"dateFormatItem-d": "d",
	"quarters-format-wide": [
		"1-й квартал",
		"2-й квартал",
		"3-й квартал",
		"4-й квартал"
	],
	"days-format-wide": [
		"воскресенье",
		"понедельник",
		"вторник",
		"среда",
		"четверг",
		"пятница",
		"суббота"
	],
	"eraNarrow": [
		"до н.э.",
		"н.э."
	]
}
//end v1.x content
);
},
'dojo/cldr/nls/number':function(){
define({ root:

//begin v1.x content
{
	"scientificFormat": "#E0",
	"currencySpacing-afterCurrency-currencyMatch": "[:letter:]",
	"infinity": "∞",
	"list": ";",
	"percentSign": "%",
	"minusSign": "-",
	"currencySpacing-beforeCurrency-surroundingMatch": "[:digit:]",
	"decimalFormat-short": "000T",
	"currencySpacing-afterCurrency-insertBetween": " ",
	"nan": "NaN",
	"nativeZeroDigit": "0",
	"plusSign": "+",
	"currencySpacing-afterCurrency-surroundingMatch": "[:digit:]",
	"currencySpacing-beforeCurrency-currencyMatch": "[:letter:]",
	"currencyFormat": "¤ #,##0.00",
	"perMille": "‰",
	"group": ",",
	"percentFormat": "#,##0%",
	"decimalFormat": "#,##0.###",
	"decimal": ".",
	"patternDigit": "#",
	"currencySpacing-beforeCurrency-insertBetween": " ",
	"exponential": "E"
}
//end v1.x content
,
	"ar": true,
	"ca": true,
	"cs": true,
	"da": true,
	"de": true,
	"el": true,
	"en": true,
	"en-au": true,
	"en-gb": true,
	"es": true,
	"fi": true,
	"fr": true,
	"fr-ch": true,
	"he": true,
	"hu": true,
	"it": true,
	"ja": true,
	"ko": true,
	"nb": true,
	"nl": true,
	"pl": true,
	"pt": true,
	"pt-pt": true,
	"ro": true,
	"ru": true,
	"sk": true,
	"sl": true,
	"sv": true,
	"th": true,
	"tr": true,
	"zh": true,
	"zh-hant": true,
	"zh-hk": true
});
},
'dijit/nls/ru/loading':function(){
define(
//begin v1.x content
({
	loadingState: "Загрузка...",
	errorState: "Извините, возникла ошибка"
})
//end v1.x content
);

},
'dijit/form/nls/ru/Textarea':function(){
define(
//begin v1.x content
({
	iframeEditTitle: 'область редактирования',  // primary title for editable IFRAME, for screen readers when focus is in the editing area
	iframeFocusTitle: 'фрейм области редактирования'  // secondary title for editable IFRAME when focus is on outer container
									 //  to let user know that focus has moved out of editing area and to the
									 //  parent element of the editing area
})
//end v1.x content
);

}}});

define("uman/uman_ru", [], 1);
