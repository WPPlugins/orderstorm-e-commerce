/*
Got this snippet from:
=====================
http://javascript.crockford.com/remedial.html
*/
function typeOf(value) {
	var s = typeof value;
	if (s === 'object') {
		if (value) {
			if (Object.prototype.toString.call(value) == '[object Array]') {
				s = 'array';
			}
		} else {
			s = 'null';
		}
	}
	return s;
}

function isEmpty(o) {
	var i, v;
	if (typeOf(o) === 'object') {
		for (i in o) {
			v = o[i];
			if (v !== undefined && typeOf(v) !== 'function') {
				return false;
			}
		}
	}
	return true;
}

if (!String.prototype.entityify) {
	String.prototype.entityify = function () {
		return this.replace(/&/g, "&amp;").replace(/</g,
			"&lt;").replace(/>/g, "&gt;");
	};
}

if (!String.prototype.quote) {
	String.prototype.quote = function () {
		var c, i, l = this.length, o = '"';
		for (i = 0; i < l; i += 1) {
			c = this.charAt(i);
			if (c >= ' ') {
				if (c === '\\' || c === '"') {
					o += '\\';
				}
				o += c;
			} else {
				switch (c) {
				case '\b':
					o += '\\b';
					break;
				case '\f':
					o += '\\f';
					break;
				case '\n':
					o += '\\n';
					break;
				case '\r':
					o += '\\r';
					break;
				case '\t':
					o += '\\t';
					break;
				default:
					c = c.charCodeAt();
					o += '\\u00' + Math.floor(c / 16).toString(16) +
						(c % 16).toString(16);
				}
			}
		}
		return o + '"';
	};
} 

if (!String.prototype.supplant) {
	String.prototype.supplant = function (o) {
		return this.replace(/{([^{}]*)}/g,
			function (a, b) {
				var r = o[b];
				return typeof r === 'string' || typeof r === 'number' ? r : a;
			}
		);
	};
}

if (!String.prototype.trim) {
	String.prototype.trim = function () {
		return this.replace(/^\s*(\S*(?:\s+\S+)*)\s*$/, "$1");
	};
}
		
/*
Got this snippet from:
=====================
https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Object/keys
*/
if (!Object.keys) {
	Object.keys = (function () {
	var hasOwnProperty = Object.prototype.hasOwnProperty,
		hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
		dontEnums = [
			'toString',
			'toLocaleString',
			'valueOf',
			'hasOwnProperty',
			'isPrototypeOf',
			'propertyIsEnumerable',
			'constructor'
		],
		dontEnumsLength = dontEnums.length;

	return function (obj) {
		if (typeof obj !== 'object' && typeof obj !== 'function' || obj === null) throw new TypeError('Object.keys called on non-object');

		var result = [];

		for (var prop in obj) {
			if (hasOwnProperty.call(obj, prop)) result.push(prop);
		}

		if (hasDontEnumBug) {
			for (var i=0; i < dontEnumsLength; i++) {
				if (hasOwnProperty.call(obj, dontEnums[i])) result.push(dontEnums[i]);
			}
		}
		return result;
	};
	})();
}

/*
Got this snippet from:
=====================
https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/indexOf
*/
if (!Array.prototype.indexOf)
{
	Array.prototype.indexOf = function (searchElement)
	{
		"use strict";
		if (this === void 0 || this === null)
		{
			throw new TypeError();
		}
		var t = Object(this);
		var len = t.length >>> 0;
		if (len === 0)
		{
			return -1;
		}
		var n = 0;
		if (arguments.length > 0)
		{
			n = Number(arguments[1]);
			// shortcut for verifying if it's NaN
			if (n !== n)
			{
				n = 0;
			}
			else
			{
				if (n !== 0 && n !== Infinity && n !== -Infinity)
				{
					n = (n > 0 || -1) * Math.floor(Math.abs(n));
				}
			}
		}
		if (n >= len)
		{
			return -1;
		}
		var k = n >= 0 ? n : Math.max(len - Math.abs(n), 0);
		for (; k < len; k++)
		{
			if (k in t && t[k] === searchElement)
			{
				return k;
			}
		}
		return -1;
	};
}

/*
Got this snippet from:
=====================
https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/Array/forEach
*/
// Production steps of ECMA-262, Edition 5, 15.4.4.18
// Reference: http://es5.github.com/#x15.4.4.18
if ( !Array.prototype.forEach ) {

  Array.prototype.forEach = function( callback, thisArg ) {

    var T, k;

    if ( this === null ) {
      throw new TypeError( "this is null or not defined" );
    }

    // 1. Let O be the result of calling ToObject passing the |this| value as the argument.
    var O = Object(this);

    // 2. Let lenValue be the result of calling the Get internal method of O with the argument "length".
    // 3. Let len be ToUint32(lenValue).
    var len = O.length >>> 0; // Hack to convert O.length to a UInt32

    // 4. If IsCallable(callback) is false, throw a TypeError exception.
    // See: http://es5.github.com/#x9.11
    if ( {}.toString.call(callback) != "[object Function]" ) {
      throw new TypeError( callback + " is not a function" );
    }

    // 5. If thisArg was supplied, let T be thisArg; else let T be undefined.
    if ( thisArg ) {
      T = thisArg;
    }

    // 6. Let k be 0
    k = 0;

    // 7. Repeat, while k < len
    while( k < len ) {

      var kValue;

      // a. Let Pk be ToString(k).
      //   This is implicit for LHS operands of the in operator
      // b. Let kPresent be the result of calling the HasProperty internal method of O with argument Pk.
      //   This step can be combined with c
      // c. If kPresent is true, then
      if ( k in O ) {

        // i. Let kValue be the result of calling the Get internal method of O with argument Pk.
        kValue = O[ k ];

        // ii. Call the Call internal method of callback with T as the this value and
        // argument list containing kValue, k, and O.
        callback.call( T, kValue, k, O );
      }
      // d. Increase k by 1.
      k++;
    }
    // 8. return undefined
  };
}

/*
Got this snippet from:
=====================
http://stackoverflow.com/questions/5306680/move-an-array-element-from-one-array-position-to-another
http://jsperf.com/arraymove-many-sizes
Created by: Andrew Backer <http://stackoverflow.com/users/15127/andrew-backer>
*/
if (!Array.prototype.move)
{
	Array.prototype.move = function (from, to) {
		if ( Math.abs(from - to) > 60) {
			this.splice(to, 0, this.splice(from, 1)[0]);
		} else {
			// works better when we are not moving things very far
			var target = this[from];
			var inc = (to - from) / Math.abs(to - from);
			var current = from;
			for (; current != to; current += inc) {
				this[current] = this[current + inc];
			}
			this[to] = target;    
		}
	};
}
