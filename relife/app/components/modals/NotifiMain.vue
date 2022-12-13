<template>
    <transition name="overlay">
        <div class="overlay" v-if="notification.show">
            <div class="modal loader"
                :class="{'loader--success': notification.status === 1, 'loader--error': notification.status === 2}"
            >
                <loader-item class="preloader--big" :stroke-width="2" />
                <template v-if="notification.status === 1">
                    <div class="loader__icon">
                        <ion-icon class="success-text" :icon="require('@/assets/icons/succsess-icon.svg')"></ion-icon>
                    </div>
                    <h1>Успешно.</h1>
                </template>
                <template v-if="notification.status === 2">
                    <div class="loader__icon">
                        <ion-icon class="error-text" :icon="require('@/assets/icons/error-icon.svg')"></ion-icon>
                    </div>
                    <h1>Ошибка.</h1>
                </template>
            </div>
        </div>
    </transition>
</template>

<script>
import LoaderItem from '@/components/common/Loader.vue';
import { IonIcon } from '@ionic/vue';
import { mapGetters, mapMutations } from 'vuex'

export default {
    name: "LoaderModal",
    components: { LoaderItem, IonIcon },
    computed: {
        ...mapGetters({
            notification: 'common/GET_NOTIFICATION'
        })
    },
    methods: {
        ...mapMutations({
            setNotificationShow: 'common/SET_NOTIFICATION_SHOW',
        }),
        closeLoader() {
            this.setNotificationShow(false);
        }
    }
}
</script>
