import {createI18n} from "vue-i18n";
import en from '../lang/en';
import ru from '../lang/ru';

const i18n = createI18n({
    locale: 'ru',
    fallbackLocale: 'ru',
    messages: {
        en, ru
    },
})

export default i18n;
