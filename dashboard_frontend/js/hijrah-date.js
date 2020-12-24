(function (global, factory) {typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :typeof define === 'function' && define.amd ? define(factory) :global.HijrahDate = factory()}(this, function(){ 'use strict';var slice             = [].slice,
    splice            = [].splice,
    push              = [].push,
    getPrototypeOf    = Object.getPrototypeOf;

var lowercase = function(string) {return isString(string) ? string.toLowerCase() : string;};
var uppercase = function(string) {return isString(string) ? string.toUpperCase() : string;};

function toInt(value){
	return parseInt(value, 10);
}

function isUndefined(value) {return typeof value === 'undefined';}

function isDefined(value) {return typeof value !== 'undefined';}

function isString(value) {return typeof value === 'string';}

function isNumber(value) {return typeof value === 'number';}

function isDate(value) {
  return Object.prototype.toString.call(value) === '[object Date]';
}

function isValidDate(value) {
  return isDate(value) && !isNaN(value.getTime());
}

var isArray = Array.isArray;

function isFunction(value) {return typeof value === 'function';}

function concat(array1, array2, index) {
  return array1.concat(slice.call(array2, index));
}

function forEach(obj, iterator, context) {
  var key, length;
  if (obj) {
    if (isFunction(obj)) {
      for (key in obj) {
        if (key !== 'prototype' && key !== 'length' && key !== 'name' && obj.hasOwnProperty(key)) {
          iterator.call(context, obj[key], key, obj);
        }
      }
    } else if (isArray(obj) || isArrayLike(obj)) {
      var isPrimitive = typeof obj !== 'object';
      for (key = 0, length = obj.length; key < length; key++) {
        if (isPrimitive || key in obj) {
          iterator.call(context, obj[key], key, obj);
        }
      }
    } else if (obj.forEach && obj.forEach !== forEach) {
        obj.forEach(iterator, context, obj);
    } else if (isBlankObject(obj)) {
      // createMap() fast path --- Safe to avoid hasOwnProperty check because prototype chain is empty
      for (key in obj) {
        iterator.call(context, obj[key], key, obj);
      }
    } else if (typeof obj.hasOwnProperty === 'function') {
      // Slow path for objects inheriting Object.prototype, hasOwnProperty check needed
      for (key in obj) {
        if (obj.hasOwnProperty(key)) {
          iterator.call(context, obj[key], key, obj);
        }
      }
    } else {
      // Slow path for objects which do not have a method `hasOwnProperty`
      for (key in obj) {
        if (hasOwnProperty.call(obj, key)) {
          iterator.call(context, obj[key], key, obj);
        }
      }
    }
  }
  return obj;
}

function floorDiv(x, y){
	var r = toInt(x / y);
	if ((x ^ y) < 0 && (r * y != x)) {
		r--;
	}
	return r;
}

function floorMod(x, y){
	return x - floorDiv(x, y) * y;
}

function addExact(x, y) {
	var r = x + y;
	if (((x ^ r) & (y ^ r)) < 0) {
		throw "integer overflow";
	}
	return r;
}

function arrayBinarySearch(a, key) {
	var low = 0;
	var high = a.length - 1;

	while (low <= high) {
		var mid = (low + high) >>> 1;
		var midVal = a[mid];

		if (midVal < key){
			low = mid + 1;
		} else if (midVal > key){
			high = mid - 1;
		} else {
			return mid;
		}
	}
	return -(low + 1);  // key not found.
}

var LocaleProvider = (function(){
	var locales = {};

	return {
		register: function(localId, localeData){
			locales[localId] = localeData;
		},
		getLocale: function(localId){
			return locales[localId];
		}
	};
})();
/*
Original work Copyright (c) 2010-2016 Google, Inc. http://angularjs.org
Modified work Copyright (c) 2016 Mouaffak A. Sarhan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

'use strict';
(function(){
var PLURAL_CATEGORY = {ZERO: "zero", ONE: "one", TWO: "two", FEW: "few", MANY: "many", OTHER: "other"};
var locale = {
  "DATETIME_FORMATS": {
    "AMPMS": [
      "\u0635",
      "\u0645"
    ],
    "DAY": [
      "\u0627\u0644\u0623\u062d\u062f",
      "\u0627\u0644\u0627\u062b\u0646\u064a\u0646",
      "\u0627\u0644\u062b\u0644\u0627\u062b\u0627\u0621",
      "\u0627\u0644\u0623\u0631\u0628\u0639\u0627\u0621",
      "\u0627\u0644\u062e\u0645\u064a\u0633",
      "\u0627\u0644\u062c\u0645\u0639\u0629",
      "\u0627\u0644\u0633\u0628\u062a"
    ],
    "ERANAMES": [
      "\u0647\u062C\u0631\u064A"
    ],
    "ERAS": [
      "\u0647\u0640"
    ],
    "FIRSTDAYOFWEEK": 5,
    "MONTH": [
      "\u0645\u062D\u0631\u0645",
      "\u0635\u0641\u0631",
      "\u0631\u0628\u064A\u0639 \u0627\u0644\u0623\u0648\u0644",
      "\u0631\u0628\u064A\u0639 \u0627\u0644\u062B\u0627\u0646\u064A",
      "\u062C\u0645\u0627\u062F\u0649 \u0627\u0644\u0623\u0648\u0644\u0649",
      "\u062C\u0645\u0627\u062F\u0649 \u0627\u0644\u0622\u062E\u0631\u0629",
      "\u0631\u062C\u0628",
      "\u0634\u0639\u0628\u0627\u0646",
      "\u0631\u0645\u0636\u0627\u0646",
      "\u0634\u0648\u0627\u0644",
      "\u0630\u0648 \u0627\u0644\u0642\u0639\u062F\u0629",
      "\u0630\u0648 \u0627\u0644\u062D\u062C\u0629"
     ],
    "SHORTDAY": [
      "\u0627\u0644\u0623\u062d\u062f",
      "\u0627\u0644\u0627\u062b\u0646\u064a\u0646",
      "\u0627\u0644\u062b\u0644\u0627\u062b\u0627\u0621",
      "\u0627\u0644\u0623\u0631\u0628\u0639\u0627\u0621",
      "\u0627\u0644\u062e\u0645\u064a\u0633",
      "\u0627\u0644\u062c\u0645\u0639\u0629",
      "\u0627\u0644\u0633\u0628\u062a"
    ],
    "SHORTMONTH": [
		"\u0645\u062D\u0631\u0645",
		"\u0635\u0641\u0631",
		"\u0631\u0628\u064A\u0639 1",
		"\u0631\u0628\u064A\u0639 2",
		"\u062C\u0645\u0627\u062F\u0649 1",
		"\u062C\u0645\u0627\u062F\u0649 2",
		"\u0631\u062C\u0628",
		"\u0634\u0639\u0628\u0627\u0646",
		"\u0631\u0645\u0636\u0627\u0646",
		"\u0634\u0648\u0627\u0644",
		"\u0630\u0648 \u0627\u0644\u0642\u0639\u062F\u0629",
		"\u0630\u0648 \u0627\u0644\u062D\u062C\u0629"
	],
    "STANDALONEMONTH": [
      "\u0645\u062D\u0631\u0645",
      "\u0635\u0641\u0631",
      "\u0631\u0628\u064A\u0639 \u0627\u0644\u0623\u0648\u0644",
      "\u0631\u0628\u064A\u0639 \u0627\u0644\u062B\u0627\u0646\u064A",
      "\u062C\u0645\u0627\u062F\u0649 \u0627\u0644\u0623\u0648\u0644\u0649",
      "\u062C\u0645\u0627\u062F\u0649 \u0627\u0644\u0622\u062E\u0631\u0629",
      "\u0631\u062C\u0628",
      "\u0634\u0639\u0628\u0627\u0646",
      "\u0631\u0645\u0636\u0627\u0646",
      "\u0634\u0648\u0627\u0644",
      "\u0630\u0648 \u0627\u0644\u0642\u0639\u062F\u0629",
      "\u0630\u0648 \u0627\u0644\u062D\u062C\u0629"
    ],
    "WEEKENDRANGE": [
      4,
      5
    ],
    "fullDate": "EEEE\u060c d MMMM\u060c y",
    "longDate": "d MMMM\u060c y",
    "medium": "dd\u200f/MM\u200f/y h:mm:ss a",
    "mediumDate": "dd\u200f/MM\u200f/y",
    "mediumTime": "h:mm:ss a",
    "short": "d\u200f/M\u200f/y h:mm a",
    "shortDate": "d\u200f/M\u200f/y",
    "shortTime": "h:mm a"
  },
  "NUMBER_FORMATS": {
    "CURRENCY_SYM": "\u00a3",
    "DECIMAL_SEP": "\u066b",
    "GROUP_SEP": "\u066c",
    "PATTERNS": [
      {
        "gSize": 3,
        "lgSize": 3,
        "maxFrac": 3,
        "minFrac": 0,
        "minInt": 1,
        "negPre": "-",
        "negSuf": "",
        "posPre": "",
        "posSuf": ""
      },
      {
        "gSize": 3,
        "lgSize": 3,
        "maxFrac": 2,
        "minFrac": 2,
        "minInt": 1,
        "negPre": "-\u00a4\u00a0",
        "negSuf": "",
        "posPre": "\u00a4\u00a0",
        "posSuf": ""
      }
    ]
  },
  "id": "ar",
  "localeID": "ar",
  "pluralCat": function(n, opt_precision) {  if (n == 0) {    return PLURAL_CATEGORY.ZERO;  }  if (n == 1) {    return PLURAL_CATEGORY.ONE;  }  if (n == 2) {    return PLURAL_CATEGORY.TWO;  }  if (n % 100 >= 3 && n % 100 <= 10) {    return PLURAL_CATEGORY.FEW;  }  if (n % 100 >= 11 && n % 100 <= 99) {    return PLURAL_CATEGORY.MANY;  }  return PLURAL_CATEGORY.OTHER;}
};

LocaleProvider.register('ar', locale);

})();

/*
Original work Copyright (c) 2010-2016 Google, Inc. http://angularjs.org
Modified work Copyright (c) 2016 Mouaffak A. Sarhan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

'use strict';
(function(){
var PLURAL_CATEGORY = {ZERO: "zero", ONE: "one", TWO: "two", FEW: "few", MANY: "many", OTHER: "other"};

function getDecimals(n) {
  n = n + '';
  var i = n.indexOf('.');
  return (i == -1) ? 0 : n.length - i - 1;
}

function getVF(n, opt_precision) {
  var v = opt_precision;

  if (undefined === v) {
    v = Math.min(getDecimals(n), 3);
  }

  var base = Math.pow(10, v);
  var f = ((n * base) | 0) % base;
  return {v: v, f: f};
}

var locale = {
  "DATETIME_FORMATS": {
    "AMPMS": [
      "AM",
      "PM"
    ],
    "DAY": [
      "Sunday",
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday"
    ],
    "ERANAMES": [
      "Anno Hegirae"
    ],
    "ERAS": [
      "AH"
    ],
    "FIRSTDAYOFWEEK": 6,
    "MONTH": [
      "Muharram",
      "Safar",
      "Rabiʻ I",
      "Rabiʻ II",
      "Jumada I",
      "Jumada II",
      "Rajab",
      "Shaʻban",
      "Ramadan",
      "Shawwal",
      "Dhuʻl-Qiʻdah",
      "Dhuʻl-Hijjah"
    ],
    "SHORTDAY": [
      "Sun",
      "Mon",
      "Tue",
      "Wed",
      "Thu",
      "Fri",
      "Sat"
    ],
    "SHORTMONTH": [
      "Muh",
      "Saf",
      "Rab I",
      "Rab II",
      "Jum I",
      "Jum II",
      "Raj",
      "Sha",
      "Ram",
      "Shaw",
      "Dhuʻl-Q",
      "Dhuʻl-H"
    ],
    "STANDALONEMONTH": [
      "Muharram",
      "Safar",
      "Rabiʻ I",
      "Rabiʻ II",
      "Jumada I",
      "Jumada II",
      "Rajab",
      "Shaʻban",
      "Ramadan",
      "Shawwal",
      "Dhuʻl-Qiʻdah",
      "Dhuʻl-Hijjah"
    ],
    "WEEKENDRANGE": [
      5,
      6
    ],
    "fullDate": "EEEE, MMMM d, y",
    "longDate": "MMMM d, y",
    "medium": "MMM d, y h:mm:ss a",
    "mediumDate": "MMM d, y",
    "mediumTime": "h:mm:ss a",
    "short": "M/d/yy h:mm a",
    "shortDate": "M/d/yy",
    "shortTime": "h:mm a"
  },
  "NUMBER_FORMATS": {
    "CURRENCY_SYM": "$",
    "DECIMAL_SEP": ".",
    "GROUP_SEP": ",",
    "PATTERNS": [
      {
        "gSize": 3,
        "lgSize": 3,
        "maxFrac": 3,
        "minFrac": 0,
        "minInt": 1,
        "negPre": "-",
        "negSuf": "",
        "posPre": "",
        "posSuf": ""
      },
      {
        "gSize": 3,
        "lgSize": 3,
        "maxFrac": 2,
        "minFrac": 2,
        "minInt": 1,
        "negPre": "-\u00a4",
        "negSuf": "",
        "posPre": "\u00a4",
        "posSuf": ""
      }
    ]
  },
  "id": "en",
  "localeID": "en",
  "pluralCat": function(n, opt_precision) {  var i = n | 0;  var vf = getVF(n, opt_precision);  if (i == 1 && vf.v == 0) {    return PLURAL_CATEGORY.ONE;  }  return PLURAL_CATEGORY.OTHER;}
};

LocaleProvider.register('en', locale);

})();

/*
Original work Copyright (c) 2010-2016 Google, Inc. http://angularjs.org
Modified work Copyright (c) 2016 Mouaffak A. Sarhan

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

var HijrahDateFormatter = (function(){
'use strict';


var MAX_DIGITS = 22;
var DECIMAL_SEP = '.';
var ZERO_CHAR = '0';

var ALL_COLONS = /:/g;
function timezoneToOffset(timezone, fallback) {
  // IE/Edge do not "understand" colon (`:`) in timezone
  timezone = timezone.replace(ALL_COLONS, '');
  var requestedTimezoneOffset = Date.parse('Jan 01, 1970 00:00:00 ' + timezone) / 60000;
  return isNaN(requestedTimezoneOffset) ? fallback : requestedTimezoneOffset;
}

function addDateMinutes(date, minutes) {
  date = new Date(date.getTime());
  date.setMinutes(date.getMinutes() + minutes);
  return date;
}

function convertTimezoneToLocal(date, timezone, reverse) {
  reverse = reverse ? -1 : 1;
  var dateTimezoneOffset = date.getTimezoneOffset();
  var timezoneOffset = timezoneToOffset(timezone, dateTimezoneOffset);
  return addDateMinutes(date, reverse * (timezoneOffset - dateTimezoneOffset));
}

/**
 * Parse a number (as a string) into three components that can be used
 * for formatting the number.
 *
 * (Significant bits of this parse algorithm came from https://github.com/MikeMcl/big.js/)
 *
 * @param  {string} numStr The number to parse
 * @return {object} An object describing this number, containing the following keys:
 *  - d : an array of digits containing leading zeros as necessary
 *  - i : the number of the digits in `d` that are to the left of the decimal point
 *  - e : the exponent for numbers that would need more than `MAX_DIGITS` digits in `d`
 *
 */
function parse(numStr) {
  var exponent = 0, digits, numberOfIntegerDigits;
  var i, j, zeros;

  // Decimal point?
  if ((numberOfIntegerDigits = numStr.indexOf(DECIMAL_SEP)) > -1) {
    numStr = numStr.replace(DECIMAL_SEP, '');
  }

  // Exponential form?
  if ((i = numStr.search(/e/i)) > 0) {
    // Work out the exponent.
    if (numberOfIntegerDigits < 0) numberOfIntegerDigits = i;
    numberOfIntegerDigits += +numStr.slice(i + 1);
    numStr = numStr.substring(0, i);
  } else if (numberOfIntegerDigits < 0) {
    // There was no decimal point or exponent so it is an integer.
    numberOfIntegerDigits = numStr.length;
  }

  // Count the number of leading zeros.
  for (i = 0; numStr.charAt(i) === ZERO_CHAR; i++) { /* empty */ }

  if (i === (zeros = numStr.length)) {
    // The digits are all zero.
    digits = [0];
    numberOfIntegerDigits = 1;
  } else {
    // Count the number of trailing zeros
    zeros--;
    while (numStr.charAt(zeros) === ZERO_CHAR) zeros--;

    // Trailing zeros are insignificant so ignore them
    numberOfIntegerDigits -= i;
    digits = [];
    // Convert string to array of digits without leading/trailing zeros.
    for (j = 0; i <= zeros; i++, j++) {
      digits[j] = +numStr.charAt(i);
    }
  }

  // If the number overflows the maximum allowed digits then use an exponent.
  if (numberOfIntegerDigits > MAX_DIGITS) {
    digits = digits.splice(0, MAX_DIGITS - 1);
    exponent = numberOfIntegerDigits - 1;
    numberOfIntegerDigits = 1;
  }

  return { d: digits, e: exponent, i: numberOfIntegerDigits };
}

/**
 * Round the parsed number to the specified number of decimal places
 * This function changed the parsedNumber in-place
 */
function roundNumber(parsedNumber, fractionSize, minFrac, maxFrac) {
    var digits = parsedNumber.d;
    var fractionLen = digits.length - parsedNumber.i;

    // determine fractionSize if it is not specified; `+fractionSize` converts it to a number
    fractionSize = (isUndefined(fractionSize)) ? Math.min(Math.max(minFrac, fractionLen), maxFrac) : +fractionSize;

    // The index of the digit to where rounding is to occur
    var roundAt = fractionSize + parsedNumber.i;
    var digit = digits[roundAt];

    if (roundAt > 0) {
      // Drop fractional digits beyond `roundAt`
      digits.splice(Math.max(parsedNumber.i, roundAt));

      // Set non-fractional digits beyond `roundAt` to 0
      for (var j = roundAt; j < digits.length; j++) {
        digits[j] = 0;
      }
    } else {
      // We rounded to zero so reset the parsedNumber
      fractionLen = Math.max(0, fractionLen);
      parsedNumber.i = 1;
      digits.length = Math.max(1, roundAt = fractionSize + 1);
      digits[0] = 0;
      for (var i = 1; i < roundAt; i++) digits[i] = 0;
    }

    if (digit >= 5) {
      if (roundAt - 1 < 0) {
        for (var k = 0; k > roundAt; k--) {
          digits.unshift(0);
          parsedNumber.i++;
        }
        digits.unshift(1);
        parsedNumber.i++;
      } else {
        digits[roundAt - 1]++;
      }
    }

    // Pad out with zeros to get the required fraction length
    for (; fractionLen < Math.max(0, fractionSize); fractionLen++) digits.push(0);


    // Do any carrying, e.g. a digit was rounded up to 10
    var carry = digits.reduceRight(function(carry, d, i, digits) {
      d = d + carry;
      digits[i] = d % 10;
      return Math.floor(d / 10);
    }, 0);
    if (carry) {
      digits.unshift(carry);
      parsedNumber.i++;
    }
}

/**
 * Format a number into a string
 * @param  {number} number       The number to format
 * @param  {{
 *           minFrac, // the minimum number of digits required in the fraction part of the number
 *           maxFrac, // the maximum number of digits required in the fraction part of the number
 *           gSize,   // number of digits in each group of separated digits
 *           lgSize,  // number of digits in the last group of digits before the decimal separator
 *           negPre,  // the string to go in front of a negative number (e.g. `-` or `(`))
 *           posPre,  // the string to go in front of a positive number
 *           negSuf,  // the string to go after a negative number (e.g. `)`)
 *           posSuf   // the string to go after a positive number
 *         }} pattern
 * @param  {string} groupSep     The string to separate groups of number (e.g. `,`)
 * @param  {string} decimalSep   The string to act as the decimal separator (e.g. `.`)
 * @param  {[type]} fractionSize The size of the fractional part of the number
 * @return {string}              The number formatted as a string
 */
function formatNumber(number, pattern, groupSep, decimalSep, fractionSize) {

  if (!(isString(number) || isNumber(number)) || isNaN(number)) return '';

  var isInfinity = !isFinite(number);
  var isZero = false;
  var numStr = Math.abs(number) + '',
      formattedText = '',
      parsedNumber;

  if (isInfinity) {
    formattedText = '\u221e';
  } else {
    parsedNumber = parse(numStr);

    roundNumber(parsedNumber, fractionSize, pattern.minFrac, pattern.maxFrac);

    var digits = parsedNumber.d;
    var integerLen = parsedNumber.i;
    var exponent = parsedNumber.e;
    var decimals = [];
    isZero = digits.reduce(function(isZero, d) { return isZero && !d; }, true);

    // pad zeros for small numbers
    while (integerLen < 0) {
      digits.unshift(0);
      integerLen++;
    }

    // extract decimals digits
    if (integerLen > 0) {
      decimals = digits.splice(integerLen, digits.length);
    } else {
      decimals = digits;
      digits = [0];
    }

    // format the integer digits with grouping separators
    var groups = [];
    if (digits.length >= pattern.lgSize) {
      groups.unshift(digits.splice(-pattern.lgSize, digits.length).join(''));
    }
    while (digits.length > pattern.gSize) {
      groups.unshift(digits.splice(-pattern.gSize, digits.length).join(''));
    }
    if (digits.length) {
      groups.unshift(digits.join(''));
    }
    formattedText = groups.join(groupSep);

    // append the decimal digits
    if (decimals.length) {
      formattedText += decimalSep + decimals.join('');
    }

    if (exponent) {
      formattedText += 'e+' + exponent;
    }
  }
  if (number < 0 && !isZero) {
    return pattern.negPre + formattedText + pattern.negSuf;
  } else {
    return pattern.posPre + formattedText + pattern.posSuf;
  }
}

function padNumber(num, digits, trim, negWrap) {
  var neg = '';
  if (num < 0 || (negWrap && num <= 0)) {
    if (negWrap) {
      num = -num + 1;
    } else {
      num = -num;
      neg = '-';
    }
  }
  num = '' + num;
  while (num.length < digits) num = ZERO_CHAR + num;
  if (trim) {
    num = num.substr(num.length - digits);
  }
  return neg + num;
}


function dateGetter(name, size, offset, trim, negWrap) {
  offset = offset || 0;
  return function(date) {
    var value = date['get' + name]();
    if (offset > 0 || value > -offset) {
      value += offset;
    }
    if (value === 0 && offset === -12) value = 12;
    return padNumber(value, size, trim, negWrap);
  };
}

function dateStrGetter(name, shortForm, standAlone) {
  return function(date, formats) {
    var value = date['get' + name]();
    var propPrefix = (standAlone ? 'STANDALONE' : '') + (shortForm ? 'SHORT' : '');
    var get = uppercase(propPrefix + name);

    return formats[get][value];
  };
}

function timeZoneGetter(date, formats, offset) {
  var zone = -1 * offset;
  var paddedZone = (zone >= 0) ? '+' : '';

  paddedZone += padNumber(Math[zone > 0 ? 'floor' : 'ceil'](zone / 60), 2) +
                padNumber(Math.abs(zone % 60), 2);

  return paddedZone;
}

function getFirstThursdayOfYear(year) {
    // 0 = index of January
    var dayOfWeekOnFirst = (new Date(year, 0, 1)).getDay();
    // 4 = index of Thursday (+1 to account for 1st = 5)
    // 11 = index of *next* Thursday (+1 account for 1st = 12)
    return new Date(year, 0, ((dayOfWeekOnFirst <= 4) ? 5 : 12) - dayOfWeekOnFirst);
}

function getThursdayThisWeek(datetime) {
    return new Date(datetime.getFullYear(), datetime.getMonth(),
      // 4 = index of Thursday
      datetime.getDate() + (4 - datetime.getDay()));
}

function weekGetter(size) {
   return function(hijrahDate) {
	   var result = toInt(((hijrahDate.getDayOfYear() - 1) / 7)) + 1;
	   /*
      var firstThurs = getFirstThursdayOfYear(date.getFullYear()),
         thisThurs = getThursdayThisWeek(date);

      var diff = +thisThurs - +firstThurs,
         result = 1 + Math.round(diff / 6.048e8); // 6.048e8 ms per week

*/
      return padNumber(result, size);
   };
}
function ampmGetter(date, formats) {
  return date.getHours() < 12 ? formats.AMPMS[0] : formats.AMPMS[1];
}

function eraGetter(date, formats) {
  return formats.ERAS[0];
}

function longEraGetter(date, formats) {
  return formats.ERANAMES[0];
}

var DATE_FORMATS = {
  yyyy: dateGetter('FullYear', 4, 0, false, true),
    yy: dateGetter('FullYear', 2, 0, true, true),
     y: dateGetter('FullYear', 1, 0, false, true),
  MMMM: dateStrGetter('Month'),
   MMM: dateStrGetter('Month', true),
    MM: dateGetter('Month', 2, 1),
     M: dateGetter('Month', 1, 1),
  LLLL: dateStrGetter('Month', false, true),
    dd: dateGetter('Date', 2),
     d: dateGetter('Date', 1),
    HH: dateGetter('Hours', 2),
     H: dateGetter('Hours', 1),
    hh: dateGetter('Hours', 2, -12),
     h: dateGetter('Hours', 1, -12),
    mm: dateGetter('Minutes', 2),
     m: dateGetter('Minutes', 1),
    ss: dateGetter('Seconds', 2),
     s: dateGetter('Seconds', 1),
     // while ISO 8601 requires fractions to be prefixed with `.` or `,`
     // we can be just safely rely on using `sss` since we currently don't support single or two digit fractions
   sss: dateGetter('Milliseconds', 3),
  EEEE: dateStrGetter('Day'),
   EEE: dateStrGetter('Day', true),
     a: ampmGetter,
     Z: timeZoneGetter,
    ww: weekGetter(2),
     w: weekGetter(1),
     G: eraGetter,
     GG: eraGetter,
     GGG: eraGetter,
     GGGG: longEraGetter
};

var DATE_FORMATS_SPLIT = /((?:[^yMLdHhmsaZEwG']+)|(?:'(?:[^']|'')*')|(?:E+|y+|M+|L+|d+|H+|h+|m+|s+|a|Z|G+|w+))(.*)/,
    NUMBER_STRING = /^\-?\d+$/;

/**
 * @ngdoc filter
 * @name date
 * @kind function
 *
 * @description
 *   Formats `date` to a string based on the requested `format`.
 *
 *   `format` string can be composed of the following elements:
 *
 *   * `'yyyy'`: 4 digit representation of year (e.g. AD 1 => 0001, AD 2010 => 2010)
 *   * `'yy'`: 2 digit representation of year, padded (00-99). (e.g. AD 2001 => 01, AD 2010 => 10)
 *   * `'y'`: 1 digit representation of year, e.g. (AD 1 => 1, AD 199 => 199)
 *   * `'MMMM'`: Month in year (January-December)
 *   * `'MMM'`: Month in year (Jan-Dec)
 *   * `'MM'`: Month in year, padded (01-12)
 *   * `'M'`: Month in year (1-12)
 *   * `'LLLL'`: Stand-alone month in year (January-December)
 *   * `'dd'`: Day in month, padded (01-31)
 *   * `'d'`: Day in month (1-31)
 *   * `'EEEE'`: Day in Week,(Sunday-Saturday)
 *   * `'EEE'`: Day in Week, (Sun-Sat)
 *   * `'HH'`: Hour in day, padded (00-23)
 *   * `'H'`: Hour in day (0-23)
 *   * `'hh'`: Hour in AM/PM, padded (01-12)
 *   * `'h'`: Hour in AM/PM, (1-12)
 *   * `'mm'`: Minute in hour, padded (00-59)
 *   * `'m'`: Minute in hour (0-59)
 *   * `'ss'`: Second in minute, padded (00-59)
 *   * `'s'`: Second in minute (0-59)
 *   * `'sss'`: Millisecond in second, padded (000-999)
 *   * `'a'`: AM/PM marker
 *   * `'Z'`: 4 digit (+sign) representation of the timezone offset (-1200-+1200)
 *   * `'ww'`: Week of year, padded (00-53). Week 01 is the week with the first Thursday of the year
 *   * `'w'`: Week of year (0-53). Week 1 is the week with the first Thursday of the year
 *   * `'G'`, `'GG'`, `'GGG'`: The abbreviated form of the era string (e.g. 'AD')
 *   * `'GGGG'`: The long form of the era string (e.g. 'Anno Domini')
 *
 *   `format` string can also be one of the following predefined
 *   {@link guide/i18n localizable formats}:
 *
 *   * `'medium'`: equivalent to `'MMM d, y h:mm:ss a'` for en_US locale
 *     (e.g. Sep 3, 2010 12:05:08 PM)
 *   * `'short'`: equivalent to `'M/d/yy h:mm a'` for en_US  locale (e.g. 9/3/10 12:05 PM)
 *   * `'fullDate'`: equivalent to `'EEEE, MMMM d, y'` for en_US  locale
 *     (e.g. Friday, September 3, 2010)
 *   * `'longDate'`: equivalent to `'MMMM d, y'` for en_US  locale (e.g. September 3, 2010)
 *   * `'mediumDate'`: equivalent to `'MMM d, y'` for en_US  locale (e.g. Sep 3, 2010)
 *   * `'shortDate'`: equivalent to `'M/d/yy'` for en_US locale (e.g. 9/3/10)
 *   * `'mediumTime'`: equivalent to `'h:mm:ss a'` for en_US locale (e.g. 12:05:08 PM)
 *   * `'shortTime'`: equivalent to `'h:mm a'` for en_US locale (e.g. 12:05 PM)
 *
 *   `format` string can contain literal values. These need to be escaped by surrounding with single quotes (e.g.
 *   `"h 'in the morning'"`). In order to output a single quote, escape it - i.e., two single quotes in a sequence
 *   (e.g. `"h 'o''clock'"`).
 *
 * @param {(Date|number|string)} date Date to format either as Date object, milliseconds (string or
 *    number) or various ISO 8601 datetime string formats (e.g. yyyy-MM-ddTHH:mm:ss.sssZ and its
 *    shorter versions like yyyy-MM-ddTHH:mmZ, yyyy-MM-dd or yyyyMMddTHHmmssZ). If no timezone is
 *    specified in the string input, the time is considered to be in the local timezone.
 * @param {string=} format Formatting rules (see Description). If not specified,
 *    `mediumDate` is used.
 * @param {string=} timezone Timezone to be used for formatting. It understands UTC/GMT and the
 *    continental US time zone abbreviations, but for general use, use a time zone offset, for
 *    example, `'+0430'` (4 hours, 30 minutes east of the Greenwich meridian)
 *    If not specified, the timezone of the browser will be used.
 * @returns {string} Formatted string or the input if input is not recognized as date/millis.
 *
 * @example
   <example name="filter-date">
     <file name="index.html">
       <span ng-non-bindable>{{1288323623006 | date:'medium'}}</span>:
           <span>{{1288323623006 | date:'medium'}}</span><br>
       <span ng-non-bindable>{{1288323623006 | date:'yyyy-MM-dd HH:mm:ss Z'}}</span>:
          <span>{{1288323623006 | date:'yyyy-MM-dd HH:mm:ss Z'}}</span><br>
       <span ng-non-bindable>{{1288323623006 | date:'MM/dd/yyyy @ h:mma'}}</span>:
          <span>{{'1288323623006' | date:'MM/dd/yyyy @ h:mma'}}</span><br>
       <span ng-non-bindable>{{1288323623006 | date:"MM/dd/yyyy 'at' h:mma"}}</span>:
          <span>{{'1288323623006' | date:"MM/dd/yyyy 'at' h:mma"}}</span><br>
     </file>
     <file name="protractor.js" type="protractor">
       it('should format date', function() {
         expect(element(by.binding("1288323623006 | date:'medium'")).getText()).
            toMatch(/Oct 2\d, 2010 \d{1,2}:\d{2}:\d{2} (AM|PM)/);
         expect(element(by.binding("1288323623006 | date:'yyyy-MM-dd HH:mm:ss Z'")).getText()).
            toMatch(/2010\-10\-2\d \d{2}:\d{2}:\d{2} (\-|\+)?\d{4}/);
         expect(element(by.binding("'1288323623006' | date:'MM/dd/yyyy @ h:mma'")).getText()).
            toMatch(/10\/2\d\/2010 @ \d{1,2}:\d{2}(AM|PM)/);
         expect(element(by.binding("'1288323623006' | date:\"MM/dd/yyyy 'at' h:mma\"")).getText()).
            toMatch(/10\/2\d\/2010 at \d{1,2}:\d{2}(AM|PM)/);
       });
     </file>
   </example>
 */
//dateFilter.$inject = ['$locale'];
function dateFilter($locale) {

  var R_ISO8601_STR = /^(\d{4})-?(\d\d)-?(\d\d)(?:T(\d\d)(?::?(\d\d)(?::?(\d\d)(?:\.(\d+))?)?)?(Z|([+-])(\d\d):?(\d\d))?)?$/;
                     // 1        2       3         4          5          6          7          8  9     10      11
  function jsonStringToDate(string) {
    var match;
    if ((match = string.match(R_ISO8601_STR))) {
      var date = new HijrahDate(new Date(0)),
          tzHour = 0,
          tzMin  = 0,
          dateSetter = match[8] ? date.setUTCFullYear : date.setFullYear,
          timeSetter = match[8] ? date.setUTCHours : date.setHours;

      if (match[9]) {
        tzHour = toInt(match[9] + match[10]);
        tzMin = toInt(match[9] + match[11]);
      }
      dateSetter.call(date, toInt(match[1]), toInt(match[2]) - 1, toInt(match[3]));
      var h = toInt(match[4] || 0) - tzHour;
      var m = toInt(match[5] || 0) - tzMin;
      var s = toInt(match[6] || 0);
      var ms = Math.round(parseFloat('0.' + (match[7] || 0)) * 1000);
      timeSetter.call(date, h, m, s, ms);
      return date;
    }
    return string;
  }

  return function(hijrahDate, format, timezone) {
    var text = '',
        parts = [],
        fn, match;

    format = format || 'mediumDate';
    format = $locale.DATETIME_FORMATS[format] || format;
    if (isString(hijrahDate)) {
      hijrahDate = NUMBER_STRING.test(hijrahDate) ? toInt(hijrahDate) : jsonStringToDate(hijrahDate);
    }

    if (isNumber(hijrahDate)) {
      hijrahDate = new HijrahDate(hijrahDate);
    }

    if (!HijrahDate.isHijrahDate(hijrahDate) || !isFinite(hijrahDate.getTime())) {
      return hijrahDate;
    }

    while (format) {
      match = DATE_FORMATS_SPLIT.exec(format);
      if (match) {
        parts = concat(parts, match, 1);
        format = parts.pop();
      } else {
        parts.push(format);
        format = null;
      }
    }

	var date = hijrahDate.toGregorian();

    var dateTimezoneOffset = date.getTimezoneOffset();
    if (timezone) {
		dateTimezoneOffset = timezoneToOffset(timezone, dateTimezoneOffset);
		date = convertTimezoneToLocal(date, timezone, true);
		hijrahDate = new HijrahDate(date);
    }
    forEach(parts, function(value) {
      fn = DATE_FORMATS[value];
      text += fn ? fn(hijrahDate, $locale.DATETIME_FORMATS, dateTimezoneOffset)
                 : value === '\'\'' ? '\'' : value.replace(/(^'|'$)/g, '').replace(/''/g, '\'');
    });

    return text;
  };
}

return {
	formatter: function(localeId){
		return dateFilter(LocaleProvider.getLocale(localeId));
	}
};

})();

'use strict';
var HijrahDate = (function(){

HijrahDate.isValid = isValid;
HijrahDate.isHijrahDate = isHijrahDate;

HijrahDate.prototype.getTime = getTime;
HijrahDate.prototype.setFullYear = setFullYear;
HijrahDate.prototype.getFullYear = getFullYear;
HijrahDate.prototype.setMonth = setMonth;
HijrahDate.prototype.getMonth = getMonth;
HijrahDate.prototype.setDate = setDate;
HijrahDate.prototype.getDate = getDate;
HijrahDate.prototype.setHours = setHours;
HijrahDate.prototype.getHours = getHours;
HijrahDate.prototype.setMinutes = setMinutes;
HijrahDate.prototype.getMinutes = getMinutes;
HijrahDate.prototype.setSeconds = setSeconds;
HijrahDate.prototype.getSeconds = getSeconds;
HijrahDate.prototype.getMilliseconds = getMilliseconds;
HijrahDate.prototype.getDayOfYear = getDayOfYear;
HijrahDate.prototype.getWeekOfYear = getWeekOfYear;
HijrahDate.prototype.getDay = getDay;
HijrahDate.prototype.getTimezoneOffset = getTimezoneOffset;
HijrahDate.prototype.getMonthLength = getMonthLength;
HijrahDate.prototype.getYearLength = getYearLength;
HijrahDate.prototype.isLeapYear = isLeapYear;
HijrahDate.prototype.toEpochDay = toEpochDay;

HijrahDate.prototype.setUTCFullYear = setUTCFullYear;
HijrahDate.prototype.getUTCFullYear = getUTCFullYear;
HijrahDate.prototype.setUTCHours = setUTCHours;
HijrahDate.prototype.getUTCHours = getUTCHours;

HijrahDate.prototype.plusYears = plusYears;
HijrahDate.prototype.plusMonths = plusMonths;
HijrahDate.prototype.plusWeeks = plusWeeks;
HijrahDate.prototype.plusDays = plusDays;
HijrahDate.prototype.minusYears = minusYears;
HijrahDate.prototype.minusMonths = minusMonths;
HijrahDate.prototype.minusWeeks = minusWeeks;
HijrahDate.prototype.minusDays = minusDays;

HijrahDate.prototype.toGregorian = toGregorian;
HijrahDate.prototype.toString = toString;
HijrahDate.prototype.format = format;

var DAYS_PER_CYCLE = 146097;
var DAYS_0000_TO_1970 = (DAYS_PER_CYCLE * 5) - (30 * 365 + 7);
var hijrahEpochMonthStartDays,
	minEpochDay,
	maxEpochDay,
	hijrahStartEpochMonth,
	minMonthLength,
	maxMonthLength,
	minYearLength,
	maxYearLength;

init();

function HijrahDate(year, monthOfYear, dayOfMonth, hours, minutes, seconds, milliseconds){
	this._year = Number.NaN;
	this._monthOfYear = Number.NaN;
	this._dayOfMonth = Number.NaN;

	var y, m, d, g;

	if(arguments.length === 0){
		g = new Date();
		var dateInfo = getHijrahDateInfo(epochDayFromGregorain(g));
		y = dateInfo[0];
		m = dateInfo[1];
		d = dateInfo[2];
	} else if(arguments.length === 1){
		if(HijrahDate.isHijrahDate(arguments[0])){
			var hd = arguments[0];
			g = hd.toGregorian();
			y = hd._year;
			m = hd._monthOfYear;
			d = hd._dayOfMonth;
		} else {
			g = new Date(arguments[0]);
			var dateInfo = getHijrahDateInfo(epochDayFromGregorain(g));
			y = dateInfo[0];
			m = dateInfo[1];
			d = dateInfo[2];
		}
	} else {
		// Computing the Gregorian day and checks the valid ranges
		g = gregorianFromEpochDay(getEpochDay(year, monthOfYear + 1, dayOfMonth || 1));
		g.setHours(hours || 0, minutes || 0, seconds || 0, milliseconds || 0);
		var dateInfo = getHijrahDateInfo(epochDayFromGregorain(g));
		y = dateInfo[0];
		m = dateInfo[1];
		d = dateInfo[2];
	}

	this._year = y;
	this._monthOfYear = m;
	this._dayOfMonth = d;
	this._gregorianDate = g;
}

function setFromHijrahDate(dest, src){
	dest._year = src._year;
	dest._monthOfYear = src._monthOfYear;
	dest._dayOfMonth = src._dayOfMonth;
	dest._gregorianDate.setFullYear(src._gregorianDate.getFullYear(), src._gregorianDate.getMonth(), src._gregorianDate.getDate());
}

function ofEpochDay(epochDay){
	var dateInfo = getHijrahDateInfo(toInt(epochDay));
	return new HijrahDate(dateInfo[0], dateInfo[1] - 1, dateInfo[2]);
}

function isValid(year, monthOfYear, dayOfMonth){
	/* this will throws an exception if values not valid */
	getEpochDay(year, monthOfYear + 1, dayOfMonth);
	return true;
}

function isHijrahDate(value) {
  return ((Object.prototype.toString.call(value) === '[object Object]') && (value instanceof HijrahDate));
}

function getTime(){
	return this._gregorianDate.getTime();
}

function setFullYear(year, month, day){
	var m = isNumber(month) ? (month + 1) : this._monthOfYear;
	var d = day || this._dayOfMonth;
	var hijrahDate = resolvePreviousValid(year, m, d);
	setFromHijrahDate(this, hijrahDate);
}
function getFullYear(){
	return this._year;
}
function setUTCFullYear(year, month, day){
	return this.setFullYear(year, month, day);
}
function getUTCFullYear(){
	return this.getFullYear();
}

function setMonth(month, day){
	var hijrahDate = resolvePreviousValid(this.getFullYear(), month + 1, day || this._dayOfMonth);
	setFromHijrahDate(this, hijrahDate);
}
function getMonth(){
	return this._monthOfYear - 1;
}

function setDate(dayOfMonth){
	if(dayOfMonth === this.getDate()){
		return;
	}

	if(dayOfMonth === 0){
		return this.minusDays(this.getDate());
	} else if(dayOfMonth < 0){
		return this.minusDays(this.getDate() + Math.abs(dayOfMonth));
	} else {
		return this.plusDays(dayOfMonth - this.getMonthLength());
	}
}
function getDate(){
	return this._dayOfMonth;
}

function setHours(hours, minutes, seconds, milliseconds){
	var res = this._gregorianDate.setHours(hours, minutes, seconds, milliseconds);
	var hijrahDate = new HijrahDate(res);
	setFromHijrahDate(this, hijrahDate);
	return res;
}
function getHours(){
	return this._gregorianDate.getHours();
}
function setUTCHours(hours, minutes, seconds, milliseconds){
	return this._gregorianDate.setUTCHours(hours, minutes, seconds, milliseconds);
}
function getUTCHours(){
	return this._gregorianDate.getUTCHours();
}

function setMinutes(minutes, seconds, milliseconds){
	return this._gregorianDate.setMinutes(minutes, seconds, milliseconds);
}
function getMinutes(){
	return this._gregorianDate.getMinutes();
}

function setSeconds(seconds, milliseconds){
	return this._gregorianDate.setSeconds(seconds, milliseconds);
}
function getSeconds(){
	return this._gregorianDate.getSeconds();
}

function getMilliseconds(){
	return this._gregorianDate.getMilliseconds();
}

function getMonthLength() {
	return _getMonthLength(this._year, this._monthOfYear);
}

function getYearLength() {
	return _getYearLength(this._year);
}

function toEpochDay() {
	return getEpochDay(this._year, this._monthOfYear, this._dayOfMonth);
}

function getDayOfYear() {
	return _getDayOfYear(this._year, this._monthOfYear) + this._dayOfMonth;
}

function getWeekOfYear() {
	return toInt(((this.getDayOfYear() - 1) / 7)) + 1;
}

function getDay() {
	return this._gregorianDate.getDay();
}

function getTimezoneOffset() {
	return this._gregorianDate.getTimezoneOffset();
}

function isLeapYear() {
	var year = this._year;
	var epochMonth = yearToEpochMonth(toInt(year));
	if (epochMonth < 0 || epochMonth > maxEpochDay) {
		throw "Hijrah date out of range";
	}
	var len = _getYearLength(toInt(year));
	return (len > 354);
}

function plusYears(years) {
	if (years == 0) {
		return this;
	}
	var newYear = this._year + years;
	var hijrahDate = resolvePreviousValid(newYear, this._monthOfYear, this._dayOfMonth);
	setFromHijrahDate(this, hijrahDate);
	return this;
}

function plusMonths(monthsToAdd) {
	if (monthsToAdd === 0) {
		return this;
	}
	var monthCount = this._year * 12 + (this._monthOfYear - 1);
	var calcMonths = monthCount + monthsToAdd;  // safe overflow
	var newYear = checkValidYear(floorDiv(calcMonths, 12));
	var newMonth = toInt(floorMod(calcMonths, 12) + 1);
	var hijrahDate = resolvePreviousValid(newYear, newMonth, this._dayOfMonth);
	setFromHijrahDate(this, hijrahDate);
	return this;
}

function plusWeeks(weeksToAdd) {
	return this.plusDays(weeksToAdd * 7);
}

function plusDays(days) {
	var hijrahDate = ofEpochDay(this.toEpochDay() + days);
	setFromHijrahDate(this, hijrahDate);
	return this;
}

function minusYears(yearsToSubtract) {
	return this.plusYears(-yearsToSubtract);
}

function minusMonths(monthsToSubtract) {
	return this.plusMonths(-monthsToSubtract);
}

function minusWeeks(weeksToSubtract) {
	return this.plusWeeks(-weeksToSubtract);
}

function minusDays(daysToSubtract) {
	return this.plusDays(-daysToSubtract);
}

function toGregorian(){
	return new Date(this._gregorianDate);
}

function format(format, localeId){
	if(arguments.length === 0){
		return HijrahDateFormatter.formatter('en')(this);
	}
	if(arguments.length === 1){
		return HijrahDateFormatter.formatter('en')(this, format);
	}
	return HijrahDateFormatter.formatter(localeId)(this, format);
}

function toString(){
	if(isNaN(this.getTime())){
		return 'Invalid Hijrah Date';
	}

	return this.format('medium');
}

function resolvePreviousValid(year, month, day) {
	var monthDays = _getMonthLength(year, month);
	if (day > monthDays) {
		day = monthDays;
	}
	return new HijrahDate(year, month-1, day);
}

function checkValidYear(hijrahYear) {
	if (hijrahYear < getMinimumYear() || hijrahYear > getMaximumYear()) {
		throw "Invalid Hijrah year: " + hijrahYear;
	}
	return toInt(hijrahYear);
}

function checkValidDayOfYear(dayOfYear) {
	if (dayOfYear < 1 || dayOfYear > getMaximumDayOfYear()) {
		throw "Invalid Hijrah day of year: " + dayOfYear;
	}
}

function checkValidMonth(month) {
	if (month < 1 || month > 12) {
		throw "Invalid Hijrah month: " + month;
	}
}

function getHijrahDateInfo(epochDay) {
	if (epochDay < minEpochDay || epochDay >= maxEpochDay) {
		throw "Hijrah date out of range";
	}

	var epochMonth = epochDayToEpochMonth(epochDay);
	var year = epochMonthToYear(epochMonth);
	var month = epochMonthToMonth(epochMonth);
	var day1 = epochMonthToEpochDay(epochMonth);
	var date = epochDay - day1; // epochDay - dayOfEpoch(year, month);

	var dateInfo = new Array(3);
	dateInfo[0] = year;
	dateInfo[1] = month + 1; // change to 1-based.
	dateInfo[2] = date + 1; // change to 1-based.
	return dateInfo;
}

function getEpochDay(hijrahYear, monthOfYear, dayOfMonth) {
	checkValidMonth(monthOfYear);
	var epochMonth = yearToEpochMonth(hijrahYear) + (monthOfYear - 1);
	if (epochMonth < 0 || epochMonth >= hijrahEpochMonthStartDays.length) {
		throw "Invalid Hijrah date, year: " +
				hijrahYear +  ", month: " + monthOfYear;
	}
	if (dayOfMonth < 1 || dayOfMonth > _getMonthLength(hijrahYear, monthOfYear)) {
		throw "Invalid Hijrah day of month: " + dayOfMonth;
	}
	return epochMonthToEpochDay(epochMonth) + (dayOfMonth - 1);
}

function _getDayOfYear(hijrahYear, month) {
	return yearMonthToDayOfYear(hijrahYear, (month - 1));
}

function _getMonthLength(hijrahYear, monthOfYear) {
	var epochMonth = yearToEpochMonth(hijrahYear) + (monthOfYear - 1);
	if (epochMonth < 0 || epochMonth >= hijrahEpochMonthStartDays.length) {
		throw "Invalid Hijrah date, year: " +
				hijrahYear +  ", month: " + monthOfYear;
	}
	return epochMonthLength(epochMonth);
}

function _getYearLength(hijrahYear) {
	return yearMonthToDayOfYear(hijrahYear, 12);
}

function getMinimumYear() {
	return epochMonthToYear(0);
}

function getMaximumYear() {
	return epochMonthToYear(hijrahEpochMonthStartDays.length - 1) - 1;
}

function getMaximumMonthLength() {
	return maxMonthLength;
}

function getMinimumMonthLength() {
	return minMonthLength;
}

function getMaximumDayOfYear() {
	return maxYearLength;
}

function getSmallestMaximumDayOfYear() {
	return minYearLength;
}

function epochDayToEpochMonth(epochDay) {
	var ndx = arrayBinarySearch(hijrahEpochMonthStartDays, epochDay);
	if (ndx < 0) {
		ndx = -ndx - 2;
	}
	return ndx;
}

function epochMonthToYear(epochMonth) {
	return toInt((epochMonth + hijrahStartEpochMonth) / 12);
}

function yearToEpochMonth(year) {
	return (year * 12) - hijrahStartEpochMonth;
}

function epochMonthToMonth(epochMonth) {
	return (epochMonth + hijrahStartEpochMonth) % 12;
}

function epochMonthToEpochDay(epochMonth) {
	return hijrahEpochMonthStartDays[epochMonth];
}

function yearMonthToDayOfYear(hijrahYear, month) {
	var epochMonthFirst = yearToEpochMonth(hijrahYear);
	return epochMonthToEpochDay(epochMonthFirst + month)
			- epochMonthToEpochDay(epochMonthFirst);
}

function epochMonthLength(epochMonth) {
	return hijrahEpochMonthStartDays[epochMonth + 1]
			- hijrahEpochMonthStartDays[epochMonth];
}

function createEpochMonths(epochDay, minYear, maxYear, years) {
    var numMonths = (maxYear - minYear + 1) * 12 + 1;
    var epochMonth = 0;
    var epochMonths = new Array(numMonths);
    minMonthLength = Number.MIN_VALUE;
    maxMonthLength = Number.MAX_VALUE;

    for (var year = minYear; year <= maxYear; year++) {
        var months = years[year];// must not be gaps
        for (var month = 0; month < 12; month++) {
            var length = months[month];
            epochMonths[epochMonth++] = epochDay;
            if (length < 29 || length > 32) {
                throw "Invalid month length in year: " + minYear;
            }
            epochDay += length;
            minMonthLength = Math.min(minMonthLength, length);
            maxMonthLength = Math.max(maxMonthLength, length);
        }
    }

    epochMonths[epochMonth++] = epochDay;

    if (epochMonth != epochMonths.length) {
        throw "Did not fill epochMonths exactly: ndx = " + epochMonth + " should be " + epochMonths.length;
    }

    return epochMonths;
}

function isGregorianLeapYear(year) {
	return ((year & 3) == 0) && ((year % 100) != 0 || (year % 400) == 0);
}

function epochDayFromGregorain(date) {
	var y = date.getFullYear();
	var m = date.getMonth()+1;
	var total = 0;
	total += 365 * y;
	if (y >= 0) {
		total += toInt((y + 3) / 4) - toInt((y + 99) / 100) + toInt((y + 399) / 400);
	} else {
		total -= toInt(y / -4 - y / -100 + y / -400);
	}
	total += toInt(((367 * m - 362) / 12));
	total += date.getDate() - 1;
	if (m > 2) {
		total--;
		if (isGregorianLeapYear(date.getFullYear()) == false) {
			total--;
		}
	}

	return total - DAYS_0000_TO_1970;
}

function gregorianFromEpochDay(epochDay) {
	var zeroDay = epochDay + DAYS_0000_TO_1970;
	// find the march-based year
	zeroDay -= 60;  // adjust to 0000-03-01 so leap day is at end of four year cycle
	var adjust = 0;
	if (zeroDay < 0) {
		// adjust negative years to positive for calculation
		var adjustCycles = toInt((zeroDay + 1) / DAYS_PER_CYCLE - 1);
		adjust = adjustCycles * 400;
		zeroDay += -adjustCycles * DAYS_PER_CYCLE;
	}
	var yearEst = toInt((400 * zeroDay + 591) / DAYS_PER_CYCLE);
	var doyEst = zeroDay - (365 * yearEst + toInt(yearEst / 4) - toInt(yearEst / 100) + toInt(yearEst / 400));
	if (doyEst < 0) {
		// fix estimate
		yearEst--;
		doyEst = zeroDay - toInt((365 * yearEst + yearEst / 4 - yearEst / 100 + yearEst / 400));
	}
	yearEst += adjust;  // reset any negative year
	var marchDoy0 = toInt(doyEst);

	// convert march-based values back to january-based
	var marchMonth0 = toInt((marchDoy0 * 5 + 2) / 153);
	var month = (marchMonth0 + 2) % 12 + 1;
	var dom = marchDoy0 - toInt((marchMonth0 * 306 + 5) / 10) + 1;
	yearEst += toInt(marchMonth0 / 10);

	return new Date(yearEst, month - 1, dom);
}

function init(){
	var years = [];
	years['1300']=[30,29,30,29,30,29,30,29,30,29,30,29];
	years['1301']=[30,30,29,30,29,30,29,30,29,30,29,29];
	years['1302']=[30,30,30,29,30,30,29,29,30,29,29,30];
	years['1303']=[29,30,30,29,30,30,29,30,29,30,29,29];
	years['1304']=[29,30,30,29,30,30,30,29,30,29,30,29];
	years['1305']=[29,29,30,30,29,30,30,29,30,30,29,29];
	years['1306']=[30,29,30,29,30,29,30,29,30,30,29,30];
	years['1307']=[29,30,29,30,29,30,29,30,29,30,29,30];
	years['1308']=[29,30,30,29,30,29,30,29,30,29,29,30];
	years['1309']=[29,30,30,30,30,29,29,30,29,29,30,29];
	years['1310']=[30,29,30,30,30,29,30,29,30,29,29,30];
	years['1311']=[29,30,29,30,30,30,29,30,29,30,29,29];
	years['1312']=[30,29,30,29,30,30,29,30,30,29,30,29];
	years['1313']=[29,30,29,30,29,30,29,30,30,30,29,29];
	years['1314']=[30,30,29,30,29,29,30,29,30,30,29,30];
	years['1315']=[29,30,30,29,30,29,29,30,29,30,29,30];
	years['1316']=[29,30,30,30,29,30,29,29,30,29,30,29];
	years['1317']=[30,29,30,30,29,30,29,30,29,30,29,29];
	years['1318']=[30,29,30,30,29,30,30,29,30,29,30,29];
	years['1319']=[29,30,29,30,30,29,30,29,30,30,29,30];
	years['1320']=[29,30,29,29,30,29,30,29,30,30,30,29];
	years['1321']=[30,29,30,29,29,30,29,29,30,30,30,30];
	years['1322']=[29,30,29,30,29,29,29,30,29,30,30,30];
	years['1323']=[29,30,30,29,30,29,29,29,30,29,30,30];
	years['1324']=[29,30,30,29,30,29,30,29,29,30,29,30];
	years['1325']=[30,29,30,29,30,30,29,30,29,30,29,30];
	years['1326']=[29,29,30,29,30,30,29,30,29,30,30,29];
	years['1327']=[30,29,29,30,29,30,29,30,30,29,30,30];
	years['1328']=[29,30,29,29,30,29,29,30,30,30,29,30];
	years['1329']=[30,29,30,29,29,30,29,29,30,30,29,30];
	years['1330']=[30,30,29,30,29,29,30,29,29,30,30,29];
	years['1331']=[30,30,29,30,30,29,29,30,29,30,29,30];
	years['1332']=[29,30,29,30,30,29,30,29,30,30,29,29];
	years['1333']=[30,29,29,30,30,29,30,30,29,30,30,29];
	years['1334']=[29,29,30,29,30,29,30,30,30,29,30,29];
	years['1335']=[30,29,30,29,29,30,29,30,30,29,30,30];
	years['1336']=[29,30,29,30,29,29,30,29,30,29,30,30];
	years['1337']=[30,29,30,29,30,29,29,30,29,30,29,30];
	years['1338']=[29,30,30,29,30,30,29,29,30,29,30,29];
	years['1339']=[30,29,30,29,30,30,30,29,30,29,29,30];
	years['1340']=[29,29,30,29,30,30,30,30,29,30,29,29];
	years['1341']=[30,29,29,30,29,30,30,30,29,30,30,29];
	years['1342']=[29,29,30,29,30,29,30,30,29,30,30,29];
	years['1343']=[30,29,29,30,29,30,29,30,29,30,30,29];
	years['1344']=[30,29,30,29,30,30,29,29,30,29,30,29];
	years['1345']=[30,29,30,30,30,29,30,29,29,30,29,29];
	years['1346']=[30,29,30,30,30,30,29,30,29,29,30,29];
	years['1347']=[29,30,29,30,30,30,29,30,30,29,29,30];
	years['1348']=[29,29,30,29,30,30,29,30,30,30,29,29];
	years['1349']=[30,29,29,30,29,30,30,29,30,30,29,30];
	years['1350']=[29,30,29,30,29,30,29,29,30,30,29,30];
	years['1351']=[30,29,30,29,30,29,30,29,29,30,29,30];
	years['1352']=[30,29,30,30,29,30,29,30,29,29,30,29];
	years['1353']=[30,29,30,30,30,29,30,29,29,30,29,30];
	years['1354']=[29,30,29,30,30,29,30,30,29,30,29,29];
	years['1355']=[30,29,29,30,30,29,30,30,29,30,30,29];
	years['1356']=[29,30,29,30,29,30,29,30,29,30,30,30];
	years['1357']=[29,29,30,29,30,29,29,30,29,30,30,30];
	years['1358']=[29,30,29,30,29,30,29,29,30,29,30,30];
	years['1359']=[29,30,30,29,30,29,30,29,29,29,30,30];
	years['1360']=[29,30,30,30,29,30,29,30,29,29,30,29];
	years['1361']=[30,29,30,30,29,30,30,29,29,30,29,30];
	years['1362']=[29,30,29,30,29,30,30,29,30,29,30,29];
	years['1363']=[30,29,30,29,30,29,30,29,30,29,30,30];
	years['1364']=[29,30,29,30,29,29,30,29,30,29,30,30];
	years['1365']=[30,30,29,29,30,29,29,30,29,30,29,30];
	years['1366']=[30,30,29,30,29,30,29,29,30,29,30,29];
	years['1367']=[30,30,29,30,30,29,30,29,29,30,29,30];
	years['1368']=[29,30,29,30,30,30,29,29,30,29,30,29];
	years['1369']=[30,29,30,29,30,30,29,30,29,30,30,29];
	years['1370']=[30,29,29,30,29,30,29,30,29,30,30,30];
	years['1371']=[29,30,29,29,30,29,30,29,30,29,30,30];
	years['1372']=[30,29,29,30,29,30,29,29,30,29,30,30];
	years['1373']=[30,29,30,29,30,29,30,29,29,30,29,30];
	years['1374']=[30,29,30,30,29,30,29,30,29,29,30,29];
	years['1375']=[30,29,30,30,29,30,30,29,30,29,30,29];
	years['1376']=[29,30,29,30,29,30,30,30,29,30,29,30];
	years['1377']=[29,29,30,29,29,30,30,30,29,30,30,29];
	years['1378']=[30,29,29,29,30,29,30,30,29,30,30,30];
	years['1379']=[29,30,29,29,29,30,29,30,30,29,30,30];
	years['1380']=[29,30,29,30,29,30,29,30,29,30,29,30];
	years['1381']=[29,30,29,30,30,29,30,29,30,29,29,30];
	years['1382']=[29,30,29,30,30,29,30,30,29,30,29,29];
	years['1383']=[30,29,29,30,30,30,29,30,30,29,30,29];
	years['1384']=[29,30,29,29,30,30,29,30,30,30,29,30];
	years['1385']=[29,29,30,29,29,30,30,29,30,30,30,29];
	years['1386']=[30,29,29,30,29,29,30,30,29,30,30,29];
	years['1387']=[30,29,30,29,30,29,30,29,30,29,30,29];
	years['1388']=[30,30,29,30,29,30,29,30,29,30,29,29];
	years['1389']=[30,30,29,30,30,29,30,30,29,29,30,29];
	years['1390']=[29,30,29,30,30,30,29,30,29,30,29,30];
	years['1391']=[29,29,30,29,30,30,29,30,30,29,30,29];
	years['1392']=[30,29,29,30,29,30,29,30,30,29,30,30];
	years['1393']=[29,30,29,29,30,29,30,29,30,29,30,30];
	years['1394']=[30,29,30,29,29,30,29,30,29,30,29,30];
	years['1395']=[30,29,30,30,29,30,29,29,30,29,29,30];
	years['1396']=[30,29,30,30,29,30,30,29,29,30,29,29];
	years['1397']=[30,29,30,30,29,30,30,30,29,29,29,30];
	years['1398']=[29,30,29,30,30,29,30,30,29,30,29,29];
	years['1399']=[30,29,30,29,30,29,30,30,29,30,29,30];
	years['1400']=[30,29,30,29,29,30,29,30,29,30,29,30];
	years['1401']=[30,30,29,30,29,29,30,29,29,30,29,30];
	years['1402']=[30,30,30,29,30,29,29,30,29,29,30,29];
	years['1403']=[30,30,30,29,30,30,29,29,30,29,29,30];
	years['1404']=[29,30,30,29,30,30,29,30,29,30,29,29];
	years['1405']=[30,29,30,29,30,30,30,29,30,29,29,30];
	years['1406']=[30,29,29,30,29,30,30,29,30,29,30,30];
	years['1407']=[29,30,29,29,30,29,30,29,30,29,30,30];
	years['1408']=[30,29,30,29,30,29,29,30,29,29,30,30];
	years['1409']=[30,30,29,30,29,30,29,29,30,29,29,30];
	years['1410']=[30,30,29,30,30,29,30,29,29,30,29,29];
	years['1411']=[30,30,29,30,30,29,30,30,29,29,30,29];
	years['1412']=[30,29,30,29,30,29,30,30,30,29,29,30];
	years['1413']=[29,30,29,29,30,29,30,30,30,29,30,29];
	years['1414']=[30,29,30,29,29,30,29,30,30,29,30,30];
	years['1415']=[29,30,29,30,29,29,30,29,30,29,30,30];
	years['1416']=[30,29,30,29,30,29,29,30,29,30,29,30];
	years['1417']=[30,29,30,30,29,29,30,29,30,29,30,29];
	years['1418']=[30,29,30,30,29,30,29,30,29,30,29,30];
	years['1419']=[29,30,29,30,29,30,29,30,30,30,29,29];
	years['1420']=[29,30,29,29,30,29,30,30,30,30,29,30];
	years['1421']=[29,29,30,29,29,29,30,30,30,30,29,30];
	years['1422']=[30,29,29,30,29,29,29,30,30,30,29,30];
	years['1423']=[30,29,30,29,30,29,29,30,29,30,29,30];
	years['1424']=[30,29,30,30,29,30,29,29,30,29,30,29];
	years['1425']=[30,29,30,30,29,30,29,30,30,29,30,29];
	years['1426']=[29,30,29,30,29,30,30,29,30,30,29,30];
	years['1427']=[29,29,30,29,30,29,30,30,29,30,30,29];
	years['1428']=[30,29,29,30,29,29,30,30,30,29,30,30];
	years['1429']=[29,30,29,29,30,29,29,30,30,29,30,30];
	years['1430']=[29,30,30,29,29,30,29,30,29,30,29,30];
	years['1431']=[29,30,30,29,30,29,30,29,30,29,29,30];
	years['1432']=[29,30,30,30,29,30,29,30,29,30,29,29];
	years['1433']=[30,29,30,30,29,30,30,29,30,29,30,29];
	years['1434']=[29,30,29,30,29,30,30,29,30,30,29,29];
	years['1435']=[30,29,30,29,30,29,30,29,30,30,29,30];
	years['1436']=[29,30,29,30,29,30,29,30,29,30,29,30];
	years['1437']=[30,29,30,30,29,29,30,29,30,29,29,30];
	years['1438']=[30,29,30,30,30,29,29,30,29,29,30,29];
	years['1439']=[30,29,30,30,30,29,30,29,30,29,29,30];
	years['1440']=[29,30,29,30,30,30,29,30,29,30,29,29];
	years['1441']=[30,29,30,29,30,30,29,30,30,29,30,29];
	years['1442']=[29,30,29,30,29,30,29,30,30,29,30,29];
	years['1443']=[30,29,30,29,30,29,30,29,30,29,30,30];
	years['1444']=[29,30,29,30,30,29,29,30,29,30,29,30];
	years['1445']=[29,30,30,30,29,30,29,29,30,29,29,30];
	years['1446']=[29,30,30,30,29,30,30,29,29,30,29,29];
	years['1447']=[30,29,30,30,30,29,30,29,30,29,30,29];
	years['1448']=[29,30,29,30,30,29,30,30,29,30,29,30];
	years['1449']=[29,29,30,29,30,29,30,30,29,30,30,29];
	years['1450']=[30,29,30,29,29,30,29,30,29,30,30,29];
	years['1451']=[30,30,30,29,29,30,29,29,30,30,29,30];
	years['1452']=[30,29,30,30,29,29,30,29,29,30,29,30];
	years['1453']=[30,29,30,30,29,30,29,30,29,29,30,29];
	years['1454']=[30,29,30,30,29,30,30,29,30,29,30,29];
	years['1455']=[29,30,29,30,30,29,30,29,30,30,29,30];
	years['1456']=[29,29,30,29,30,29,30,29,30,30,30,29];
	years['1457']=[30,29,29,30,29,29,30,29,30,30,30,30];
	years['1458']=[29,30,29,29,30,29,29,30,29,30,30,30];
	years['1459']=[29,30,30,29,29,30,29,29,30,29,30,30];
	years['1460']=[29,30,30,29,30,29,30,29,29,30,29,30];
	years['1461']=[29,30,30,29,30,29,30,29,30,30,29,29];
	years['1462']=[30,29,30,29,30,30,29,30,29,30,30,29];
	years['1463']=[29,30,29,30,29,30,29,30,30,30,29,30];
	years['1464']=[29,30,29,29,30,29,29,30,30,30,29,30];
	years['1465']=[30,29,30,29,29,30,29,29,30,30,29,30];
	years['1466']=[30,30,29,30,29,29,29,30,29,30,30,29];
	years['1467']=[30,30,29,30,30,29,29,30,29,30,29,30];
	years['1468']=[29,30,29,30,30,29,30,29,30,29,30,29];
	years['1469']=[29,30,29,30,30,29,30,30,29,30,29,30];
	years['1470']=[29,29,30,29,30,30,29,30,30,29,30,29];
	years['1471']=[30,29,29,30,29,30,29,30,30,29,30,30];
	years['1472']=[29,30,29,29,30,29,30,29,30,30,29,30];
	years['1473']=[29,30,29,30,30,29,29,30,29,30,29,30];
	years['1474']=[29,30,30,29,30,30,29,29,30,29,30,29];
	years['1475']=[29,30,30,29,30,30,30,29,29,30,29,29];
	years['1476']=[30,29,30,29,30,30,30,29,30,29,30,29];
	years['1477']=[29,30,29,29,30,30,30,30,29,30,29,30];
	years['1478']=[29,29,30,29,30,29,30,30,29,30,30,29];
	years['1479']=[30,29,29,30,29,30,29,30,29,30,30,29];
	years['1480']=[30,29,30,29,30,29,30,29,30,29,30,29];
	years['1481']=[30,29,30,30,29,30,29,30,29,30,29,29];
	years['1482']=[30,29,30,30,30,30,29,30,29,29,30,29];
	years['1483']=[29,30,29,30,30,30,29,30,30,29,29,30];
	years['1484']=[29,29,30,29,30,30,30,29,30,29,30,29];
	years['1485']=[30,29,29,30,29,30,30,29,30,30,29,30];
	years['1486']=[29,30,29,29,30,29,30,29,30,30,29,30];
	years['1487']=[30,29,30,29,30,29,29,30,29,30,29,30];
	years['1488']=[30,29,30,30,29,30,29,29,30,29,30,29];
	years['1489']=[30,29,30,30,30,29,30,29,29,30,29,30];
	years['1490']=[29,30,29,30,30,29,30,30,29,29,30,29];
	years['1491']=[30,29,29,30,30,29,30,30,29,30,29,30];
	years['1492']=[29,30,29,29,30,30,29,30,29,30,30,29];
	years['1493']=[30,29,30,29,30,29,29,30,29,30,30,30];
	years['1494']=[29,30,29,30,29,30,29,29,29,30,30,30];
	years['1495']=[29,30,30,29,30,29,29,30,29,29,30,30];
	years['1496']=[29,30,30,30,29,30,29,29,30,29,29,30];
	years['1497']=[30,29,30,30,29,30,29,30,29,30,29,30];
	years['1498']=[29,30,29,30,29,30,30,29,30,29,30,29];
	years['1499']=[30,29,30,29,29,30,30,29,30,29,30,30];
	years['1500']=[29,30,29,30,29,29,30,29,30,29,30,30];
	years['1501']=[30,29,30,29,30,29,29,29,30,29,30,30];
	years['1502']=[30,30,29,30,29,30,29,29,29,30,30,29];
	years['1503']=[30,30,29,30,30,29,30,29,29,29,30,30];
	years['1504']=[29,30,29,30,30,30,29,29,30,29,30,29];
	years['1505']=[30,29,30,29,30,30,29,30,29,30,30,29];
	years['1506']=[29,30,29,29,30,30,29,30,30,29,30,30];
	years['1507']=[29,29,30,29,29,30,30,29,30,29,30,30];
	years['1508']=[30,29,29,30,29,30,29,29,30,29,30,30];
	years['1509']=[30,29,30,29,30,29,30,29,29,30,29,30];
	years['1510']=[30,29,30,30,29,30,29,30,29,29,30,29];
	years['1511']=[30,29,30,30,29,30,30,29,30,29,29,30];
	years['1512']=[29,30,29,30,29,30,30,30,29,30,29,30];
	years['1513']=[29,29,29,30,29,30,30,30,29,30,30,29];
	years['1514']=[30,29,29,29,30,29,30,30,29,30,30,30];
	years['1515']=[29,29,30,29,29,30,29,30,30,29,30,30];
	years['1516']=[29,30,29,30,29,29,30,29,30,29,30,30];
	years['1517']=[29,30,29,30,29,30,30,29,29,30,29,30];
	years['1518']=[29,30,29,30,30,29,30,30,29,30,29,29];
	years['1519']=[30,29,29,30,30,30,29,30,30,29,30,29];
	years['1520']=[29,30,29,29,30,30,30,29,30,30,29,30];
	years['1521']=[29,29,29,30,29,30,30,29,30,30,29,30];
	years['1522']=[30,29,29,29,30,29,30,30,29,30,30,29];
	years['1523']=[30,29,30,29,30,29,30,29,29,30,30,29];
	years['1524']=[30,30,29,30,29,30,29,30,29,29,30,29];
	years['1525']=[30,30,29,30,30,29,30,29,30,29,29,30];
	years['1526']=[29,30,29,30,30,30,29,30,29,30,29,29];
	years['1527']=[30,29,30,29,30,30,29,30,30,29,30,29];
	years['1528']=[30,29,29,30,29,30,29,30,30,29,30,30];
	years['1529']=[29,30,29,29,30,29,30,29,30,29,30,30];
	years['1530']=[29,30,30,29,29,30,29,30,29,29,30,30];
	years['1531']=[29,30,30,30,29,29,30,29,30,29,29,30];
	years['1532']=[29,30,30,30,29,30,30,29,29,29,30,29];
	years['1533']=[30,29,30,30,30,29,30,29,30,29,29,30];
	years['1534']=[29,30,29,30,30,29,30,30,29,29,30,29];
	years['1535']=[30,29,30,29,30,29,30,30,29,30,29,30];
	years['1536']=[29,30,29,30,29,30,29,30,29,30,29,30];
	years['1537']=[30,29,30,30,29,29,30,29,29,30,29,30];
	years['1538']=[30,30,29,30,30,29,29,30,29,29,30,29];
	years['1539']=[30,30,30,29,30,30,29,29,30,29,29,30];
	years['1540']=[29,30,30,29,30,30,29,30,29,29,30,29];
	years['1541']=[30,29,30,29,30,30,30,29,30,29,29,30];
	years['1542']=[29,30,29,30,29,30,30,29,30,29,30,30];
	years['1543']=[29,30,29,29,30,29,30,29,30,29,30,30];
	years['1544']=[30,29,30,29,29,30,29,30,29,30,29,30];
	years['1545']=[30,30,29,30,29,29,30,29,30,29,29,30];
	years['1546']=[30,30,29,30,29,30,29,30,29,30,29,29];
	years['1547']=[30,30,29,30,30,29,30,29,30,29,30,29];
	years['1548']=[30,29,29,30,30,29,30,30,29,30,29,30];
	years['1549']=[29,30,29,29,30,29,30,30,30,29,30,29];
	years['1550']=[30,29,30,29,29,29,30,30,30,29,30,30];
	years['1551']=[29,30,29,29,30,29,29,30,30,29,30,30];
	years['1552']=[30,29,30,29,29,30,29,29,30,30,29,30];
	years['1553']=[30,29,30,29,30,29,30,29,30,29,30,29];
	years['1554']=[30,29,30,29,30,30,29,30,29,30,29,30];
	years['1555']=[29,29,30,29,30,30,29,30,30,29,30,29];
	years['1556']=[30,29,29,30,29,30,29,30,30,30,29,30];
	years['1557']=[29,30,29,29,29,30,29,30,30,30,30,29];
	years['1558']=[30,29,30,29,29,29,30,29,30,30,30,29];
	years['1559']=[30,30,29,29,30,29,29,30,30,29,30,29];
	years['1560']=[30,30,29,30,29,30,29,30,29,30,29,30];
	years['1561']=[29,30,30,29,30,29,30,30,29,29,30,29];
	years['1562']=[29,30,30,29,30,29,30,30,30,29,29,30];
	years['1563']=[29,30,29,29,30,29,30,30,30,29,30,29];
	years['1564']=[30,29,30,29,29,30,29,30,30,30,29,30];
	years['1565']=[29,30,29,30,29,29,30,29,30,30,29,30];
	years['1566']=[30,29,30,29,30,29,29,30,29,30,29,30];
	years['1567']=[30,29,30,30,29,30,29,30,29,29,30,29];
	years['1568']=[30,29,30,30,30,29,30,29,30,29,29,29];
	years['1569']=[30,29,30,30,30,29,30,30,29,30,29,29];
	years['1570']=[29,30,29,30,30,29,30,30,30,29,29,30];
	years['1571']=[29,29,30,29,30,30,29,30,30,29,30,29];
	years['1572']=[30,29,29,30,29,30,29,30,30,29,30,29];
	years['1573']=[30,29,30,30,29,30,29,29,30,29,30,29];
	years['1574']=[30,30,29,30,30,29,30,29,29,30,29,29];
	years['1575']=[30,30,30,29,30,30,29,30,29,29,29,30];
	years['1576']=[29,30,30,29,30,30,30,29,30,29,29,29];
	years['1577']=[30,29,30,30,29,30,30,29,30,29,30,29];
	years['1578']=[29,30,29,30,29,30,30,29,30,30,29,30];
	years['1579']=[29,30,29,30,29,29,30,30,29,30,29,30];
	years['1580']=[29,30,30,29,30,29,29,30,29,30,29,30];
	years['1581']=[30,30,29,30,29,30,29,29,30,29,30,29];
	years['1582']=[30,30,29,30,30,29,30,29,30,29,29,29];
	years['1583']=[30,30,29,30,30,30,29,30,29,30,29,29];
	years['1584']=[29,30,30,29,30,30,29,30,30,29,30,29];
	years['1585']=[29,30,29,30,29,30,29,30,30,29,30,30];
	years['1586']=[29,29,30,29,30,29,29,30,30,30,29,30];
	years['1587']=[29,30,30,29,29,29,30,29,30,29,30,30];
	years['1588']=[30,29,30,30,29,29,29,30,29,30,29,30];
	years['1589']=[30,29,30,30,29,30,29,29,30,29,30,29];
	years['1590']=[30,29,30,30,30,29,29,30,29,30,29,30];
	years['1591']=[29,30,29,30,30,29,30,29,30,29,30,29];
	years['1592']=[30,29,30,29,30,29,30,29,30,30,30,29];
	years['1593']=[30,29,29,30,29,29,30,29,30,30,30,29];
	years['1594']=[30,30,29,29,30,29,29,29,30,30,30,30];
	years['1595']=[29,30,29,30,29,29,30,29,29,30,30,30];
	years['1596']=[29,30,30,29,30,29,29,30,29,30,29,30];
	years['1597']=[29,30,30,29,30,29,30,29,30,29,30,29];
	years['1598']=[30,29,30,29,30,30,29,30,29,30,30,29];
	years['1599']=[29,30,29,30,29,30,29,30,30,30,29,30];
	years['1600']=[29,29,30,29,30,29,29,30,30,30,29,30];

	var minYear = 1300;
	var maxYear = 1600;
	var isoStart = epochDayFromGregorain(new Date(1882, 11-1, 12));
	hijrahStartEpochMonth = minYear * 12;
	minEpochDay = isoStart;
	hijrahEpochMonthStartDays = createEpochMonths(minEpochDay, minYear, maxYear, years);
	maxEpochDay = hijrahEpochMonthStartDays[hijrahEpochMonthStartDays.length - 1];

	minYearLength = 0, maxYearLength = 0;
	for (var year = minYear; year < maxYear; year++) {
		var length = _getYearLength(year);
		minYearLength = Math.min(minYearLength, length);
		maxYearLength = Math.max(maxYearLength, length);
	}
}

return HijrahDate;

})();
; return HijrahDate;}));