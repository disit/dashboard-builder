/**
 * Highcharts Localization plugin.
 * This plugin used for localization Highcharts date and number and also could be extend as you need.
 *
 *
 * @License GPLv3
 * @version 1.0
 * @author Milad Jafary (milad.jafary@gmail.com)
 */

function getPersianLocal() {
    var PersianLocalizationDate = {
        /**
         * Get a timestamp and return jalali date.
         * @param timestamp
         * @returns {{date: Date, hours: number, day: *, dayOfMonth: *, month: *, fullYear: *}}
         */
        getDate: function (timestamp) {
            var date = new Date(timestamp);
            return {
                date: date,
                hours: date.getHours(),
                day: date.getJalaliDay(),
                dayOfMonth: date.getJalaliDate(),
                month: date.getJalaliMonth(),
                fullYear: date.getJalaliFullYear()
            };
        }
    };

    return {
        /**
         * @type {String} , An ISO 639-1 language code
         */
        lang: 'fa',
        /**
         * @type {String} , An ISO 3166-1 language code
         */
        country: 'IR',
        date: PersianLocalizationDate,
        i18n: {
            weekdays: ['شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه'],
            months: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند']
        }
    };
}

(function (Highcharts) {

    var UNDEFINED;
    var LocalizationDate = {
        options: {
            locale: {
//                fa: {
//                    lang: 'fa',
//                    country: 'IR',
//                    date: FaLocalizationDate,
//                    i18n: {
//                        weekdays: ['شنبه', 'یکشنبه', 'دوشنبه', 'سه شنبه', 'چهارشنبه', 'پنج شنبه', 'جمعه'],
//                        months: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریرور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند']
//                    }
//                }
            }
        },

        addLocale: function (locale) {
            this.options.locale[locale.lang] = locale;
        },

        defined: function (obj) {
            return obj !== UNDEFINED && obj !== null;
        },

        pick: function () {
            var args = arguments,
                i,
                arg,
                length = args.length;
            for (i = 0; i < length; i++) {
                arg = args[i];
                if (typeof arg !== 'undefined' && arg !== null) {
                    return arg;
                }
            }
        },

        pad: function (number, length) {
            // Create an array of the remaining length +1 and join it with 0's
            return new Array((length || 2) + 1 - String(number).length).join(0) + number;
        },

        getI18nByLang: function (lang) {
            if (!this.defined(this.options.locale[lang].i18n)) {
                throw "Invalid i18n for language";
            }
            return this.options.locale[lang].i18n;
        },

        getMonthName: function (month, lang) {
            var i18n = this.getI18nByLang(lang);
            if (!this.defined(i18n.months)) {
                throw "i18n for months is undefined";
            }
            return i18n.months[month];
        },

        getWeekDay: function (weekday, lang) {
            var i18n = this.getI18nByLang(lang);
            if (!this.defined(i18n.weekdays)) {
                throw "i18n for weekdays is undefined";
            }
            return i18n.weekdays[weekday];
        },

        getDateByLocaleLang: function (localeLang) {
            if (!this.defined(this.options.locale[localeLang].date)) {
                throw "Invalid date object for selected local";
            }
            return this.options.locale[localeLang].date;
        },

        dateFormat: function (format, timestamp, capitalize, locale) {
            if (!this.defined(timestamp) || isNaN(timestamp)) {
                return 'Invalid date';
            }

            format = this.pick(format, '%Y-%m-%d %H:%M:%S');

            var lang = locale['lang'],
                localeDate = this.getDateByLocaleLang(lang).getDate(timestamp),
                date = localeDate['date'],
                hours = localeDate['hours'],
                day = localeDate['day'],
                dayOfMonth = localeDate['dayOfMonth'],
                month = localeDate['month'],
                fullYear = localeDate['fullYear'],
                key;

            // List all format keys. Custom formats can be added from the outside.
            var replacements = {
                // Day
                'a': this.getWeekDay(day, lang).substr(0, 3), // Short weekday, like 'Mon'
                'A': this.getWeekDay(day, lang), // Long weekday, like 'Monday'
                'd': this.pad(dayOfMonth), // Two digit day of the month, 01 to 31
                'e': dayOfMonth, // Day of the month, 1 through 31

                // Month
                'b': this.getMonthName(month, lang).substr(0, 3), // Short month, like 'Jan'
                'B': this.getMonthName(month, lang), // Long month, like 'January'
                'm': this.pad(month + 1), // Two digit month number, 01 through 12

                // Year
                'y': fullYear.toString().substr(2, 2), // Two digits year, like 09 for 2009
                'Y': fullYear, // Four digits year, like 2009

                // Time
                'H': this.pad(hours), // Two digits hours in 24h format, 00 through 23
                'I': this.pad((hours % 12) || 12), // Two digits hours in 12h format, 00 through 11
                'l': (hours % 12) || 12, // Hours in 12h format, 1 through 12
                'M': this.pad(date.getMinutes()), // Two digits minutes, 00 through 59
                'p': hours < 12 ? 'AM' : 'PM', // Upper case AM or PM
                'P': hours < 12 ? 'am' : 'pm', // Lower case AM or PM
                'S': this.pad(date.getSeconds()), // Two digits seconds, 00 through  59
                'L': this.pad(Math.round(timestamp % 1000), 3) // Milliseconds (naming from Ruby)
            };

            // do the replaces
            for (key in replacements) {
                while (format.indexOf('%' + key) !== -1) { // regex would do it in one line, but this is faster
                    format = format.replace('%' + key, typeof replacements[key] === 'function' ? replacements[key](timestamp) : replacements[key]);
                }
            }

            // Optionally capitalize the string and return
            return capitalize ? format.substr(0, 1).toUpperCase() + format.substr(1) : format;
        }
    };

    Highcharts.localizationDateFormat = function (format, timestamp, capitalize) {
        if (!LocalizationDate.defined(Highcharts.getOptions().locale)) {
            return Highcharts.dateFormat(format, timestamp, capitalize);
        }
        var Locale = Highcharts.getOptions().locale;
        LocalizationDate.addLocale(Locale);
        return LocalizationDate.dateFormat(format, timestamp, capitalize, Locale);
    };

    Highcharts.localizationNumber = function(number){
        if (!LocalizationDate.defined(Highcharts.getOptions().locale)) {
            return number;
        }

        return number.toString().replace(/\d+/g, function (digit) {
            var ret = '';
            for (var i = 0, len = digit.length; i < len; i++) {
                ret += String.fromCharCode(digit.charCodeAt(i) + 1728);
            }
            return ret;
        });
    }
}(Highcharts));


