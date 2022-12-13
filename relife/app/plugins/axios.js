import axios from 'axios';
import toast from '../helpers/toast'
import i18n from './i18n'
import { Preferences } from '@capacitor/preferences';

const options = {};
options.baseURL = `${process.env.VUE_APP_URL}/api`;

const instance = axios.create(options);

instance.interceptors.response.use(response => {
    return response.data.response;
}, async (error) => {
    const errors =  error?.response?.data?.response?.errors ?? error?.response?.data?.errors ?? [i18n.global.t('notifications.error_500')];
    await toast({
        message: errors.join(', <br/>'),
        class: 'error'
    });

    if (error.response.status === 401) {
        await Preferences.remove({
            key: 'token'
        })
        window.location.reload(true)
    }
    return Promise.reject(errors);
});
export default instance;
