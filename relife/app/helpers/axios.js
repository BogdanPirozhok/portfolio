import { Preferences } from '@capacitor/preferences'
import axios from '../plugins/axios'

export default async function (options) {
    const token = await Preferences.get({
        key: 'token'
    })
    const lang = await Preferences.get({
        key: 'lang'
    })

    const axiosParams = {
        url: '/v' + (options.version ?? 1) + options.url,
        method: options.method,
        headers: {
            Locale: lang.value ?? 'ru',
            'App-Version': process.env.VUE_APP_VERSION,
            ...options.headers
        },
        data: options.data,
        params: options.params
    };

    if (token.value) {
        axiosParams.headers.Authorization = `Bearer ${token.value}`
    }

    return axios(axiosParams)
        .then((res) => res)
        .catch((error) => {
            throw error;
        });
}
