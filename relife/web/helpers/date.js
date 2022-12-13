const daysDuration = function (value) {
    let countDays = value;
    let countYears = 0;

    if (value >= 365) {
        countYears = Math.floor(value / 365);
        countDays = value - 365 * countYears;
    }

    const days = countDays + (this.$i18n.locale === 'ru' ? ' д.' : ' d.');

    if (countYears) {
        const yearUnitRu = countYears < 5 ? ' г.' : ' л.';
        const years = countYears + (this.$i18n.locale === 'ru' ? yearUnitRu : ' y.');

        return `${years} ${days}`;
    }

    return days;
};

export {
    daysDuration
};
