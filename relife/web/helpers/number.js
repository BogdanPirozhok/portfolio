const numberFormat = function (number, decimals, decPoint, thousandsSep, notFloat = false) {
    if (number < 1 && !notFloat) {
        return Math.round(number * 10000) / 10000;
    }
    number = (`${number}`).replace(/[^0-9+\-Ee.]/g, '');
    const n = !isFinite(+number) ? 0 : +number;
    const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
    const dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
    let s = '';
    const toFixedFix = function (n, prec) {
        if ((`${n}`).indexOf('e') === -1) {
            return +(`${Math.round(`${n}e+${prec}`)}e-${prec}`);
        }
        const arr = (`${n}`).split('e');
        let sig = '';
        if (+arr[1] + prec > 0) {
            sig = '+';
        }
        return (+(`${Math.round(`${+arr[0]}e${sig}${+arr[1] + prec}`)}e-${prec}`)).toFixed(prec);
    };
    s = (prec ? toFixedFix(n, prec).toString() : `${Math.round(n)}`).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec && notFloat) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
};

const bigNumberFormat = function (number, decimals = 2) {
    let unit = '';
    let value = number;

    if (value >= 1000000) {
        unit = ' M';
        value /= 1000000;
    } else if (value >= 1000) {
        unit = ' K';
        value /= 1000;
    }

    return numberFormat(value, decimals, ',', ' ') + unit;
};

export {
    numberFormat,
    bigNumberFormat
};
