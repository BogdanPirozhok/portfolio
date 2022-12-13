export default function ({ $axios, app }) {
    const endpoint = 'https://dev.relifeglobal.org';

    $axios.setBaseURL(`${endpoint}/api/v1`);
    $axios.onRequest((config) => {
        // eslint-disable-next-line no-param-reassign
        config.headers = {
            Locale: app.i18n.locale || 'ru'
        };
    });

    $axios.onError((error) => {
        const { response } = error;

        if (response.data.errors && typeof response.data.errors === 'object') {
            // eslint-disable-next-line no-console
            console.log(response.data.errors);
        } else {
            // eslint-disable-next-line no-console
            console.log(error);
        }

        return Promise.reject(response);
    });

    $axios.onResponse((response) => response.data);
}
