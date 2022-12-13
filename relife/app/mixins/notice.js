import axios from "@/helpers/axios";
import { mapActions, mapGetters } from "vuex";

export default {
    data() {
        return {
            isLoadNotice: false,
        }
    },
    computed: {
        ...mapGetters({
            currentVersionApp: 'common/GET_CURRENT_VERSION_APP'
        })
    },
    methods: {
        ...mapActions({
            setReadNotifications: 'common/setReadNotifications'
        }),
        initNotice() {
            this.isLoadNotice = true;

            axios({
                method: 'get',
                url: '/user/notifications'
            })
                .then(({ data }) => {
                    this.$store.commit('common/SET_USER_NOTICES', data);

                    setTimeout(() => {
                        this.setReadNotifications();
                    }, 5000);
                })
                .finally(() => {
                    this.isLoadNotice = false;
                })
        },
        checkNotice() {
            axios({
                method: 'get',
                url: '/user/check-notifications',
                params: {
                    version_app: this.currentVersionApp
                }
            })
                .then((data) => {
                    this.$store.commit('common/SET_UPDATE_APP_MODAL', !data.is_actual_version_app);
                    this.$store.commit('common/SET_HAS_UNREAD_NOTICES', data.has_unread_notices);
                    this.$store.commit('common/SET_ALL_COUNT_UNREAD_NOTICES', data.all_count_unread_notices);
                })
        }
    },
};
