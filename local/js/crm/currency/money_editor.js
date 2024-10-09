BX.ready(function () {
    if (!BX.Currency) {
        return;
    }

    if (!BX.Currency.Editor) {
        return;
    }

    if (typeof BX.Currency?.Editor?.getUnFormattedValue_old === "undefined") {
        BX.Currency.Editor.getUnFormattedValue_old = BX.Currency.Editor.getUnFormattedValue;

        BX.Currency.Editor.getUnFormattedValue = function (value, currency) {
            let prefix = '';

            if (value.length > 0) {
                if (value.substring(0, 1) === '-') {
                    prefix = '-';
                    value = value.substring(1);
                }
            }

            let unFormattedValue = BX.Currency.Editor.getUnFormattedValue_old(value, currency);

            return prefix + unFormattedValue;
        }
    }

    if (typeof BX.Currency?.Editor?.getFormattedValue_old === "undefined") {
        BX.Currency.Editor.getFormattedValue_old = BX.Currency.Editor.getFormattedValue;

        BX.Currency.Editor.getFormattedValue = function (value, currency) {

            let prefix = '';

            if (value.length > 0) {
                if (value.substring(0, 1) === '-') {
                    prefix = '-';
                    value = value.substring(1);
                }
            }

            let formattedValue = BX.Currency.Editor.getFormattedValue_old(value, currency);

            return prefix + formattedValue;
        }
    }
});
