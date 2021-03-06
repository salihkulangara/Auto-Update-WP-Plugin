(function($, undefined) {
    'use strict';
    var PROP_MAPPING = {
        'select-one': 'defaultSelected',
        'select-multiple': 'defaultSelected',
        'radio': 'defaultChecked',
        'checkbox': 'defaultChecked'
    };

    function getPropBy(type) {
        if (type in PROP_MAPPING) {
            return PROP_MAPPING[type];
        } else {
            return 'defaultValue';
        }
    }

    function getDefaultValue() {
        var me = this;
        var type = me.prop('type');
        var defaultValue = getPropBy(type);
        var dValue;

        switch (type) {
            case 'select-one':
                dValue = this.eq(0).find('option').filter(function() {
                    return this[defaultValue];
                }).val();
                break;
            case 'select-multiple':
                dValue = [];
                this.eq(0).find('option').each(function() {
                    if (this[defaultValue]) {
                        dValue.push(this.value);
                    }
                });
                if (!dValue.length) {
                    dValue = null;
                }
                break;
            case 'checkbox':
            case 'radio':
                dValue = this.eq(0).prop(defaultValue) ? this[0].value : undefined;
                break;
            default:
                dValue = me.prop(defaultValue);
                break;
        }
        return dValue;
    }

    function setDefaultValue(updatedValue) {
        var me = this;
        var type = me.prop('type');

        var defaultValue = getPropBy(type);
        switch (type) {
            case 'select-one':
                this.find('>option').each(function(i, element) {
                    element[defaultValue] = (element.value === updatedValue.toString());
                });
                break;
            case 'select-multiple':
                var updatedValueDict = {};
                if ($.isArray(updatedValue)) {
                    for (var i = 0, ii = updatedValue.length; i < ii; i++) {
                        updatedValueDict[updatedValue[i]] = undefined;
                    }
                }
                this.find('>option').each(function(i, element) {
                    element[defaultValue] = updatedValueDict.hasOwnProperty(element.value);
                });
                break;
            case 'checkbox':
            case 'radio':
                /* falls through */
            default:
                me.prop(defaultValue, updatedValue);
                break;
        }

        return this;
    }

    $.fn.defaultValue = function() {
        if (!arguments.length) {
            return getDefaultValue.call(this);
        } else {
            return setDefaultValue.apply(this, arguments);
        }
    };

    $.fn.syncDefaultValue = function() {
        $(this).each(function(i, element) {
            var $e = $(element);
            var val;
            switch (element.type) {
                case 'checkbox':
                case 'radio':
                    val = $e.prop('checked');
                    break;
                default:
                    val = $e.val();
                    break;
            }
            if ($.isArray(val)) {
                setDefaultValue.call($e, val);
            } else {
                val = [val];
                setDefaultValue.apply($e, val);
            }
        });
        return this;
    };
})(jQuery);