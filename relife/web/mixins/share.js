import { bigNumberFormat } from '@/helpers/number';
import { daysDuration } from '@/helpers/date';

export default {
    data() {
        return {
            linkApp: null,
            windowFocus: true,
            pending: false,
            stores: {
                current: null,
                android: 'https://play.google.com/store/apps/details?id=org.relifeglobal.app',
                ios: 'https://apps.apple.com/ua/app/relife/id1644586629'
            }
        };
    },
    async created() {
        // eslint-disable-next-line nuxt/no-globals-in-created
        window.addEventListener('focus', () => {
            this.windowFocus = true;
        });

        // eslint-disable-next-line nuxt/no-globals-in-created
        window.addEventListener('blur', () => {
            this.windowFocus = false;
        });

        if (this.$device.isIos || this.$device.isAndroid) {
            this.stores.current = this.$device.isIos ? 'ios' : 'android';

            if (this.$device.isChrome || this.$device.isSafari) {
                this.linkApp = (this.$device.isChrome && this.$device.isAndroid)
                    ? `intent://${this.pathApp}#Intent;scheme=relife;end`
                    : `relife://${this.pathApp}`;
            }
        }
    },
    methods: {
        bigNumberFormat,
        daysDuration,
        goToApp() {
            this.pending = true;
            document.location = this.linkApp;

            setTimeout(() => {
                this.pending = false;

                if (this.windowFocus) {
                    document.location = this.stores[this.stores.current];
                }
            }, 2000);
        }
    }
};
