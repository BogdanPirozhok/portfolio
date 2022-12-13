<template>
    <transition name="overlay">
        <div class="overlay"
            v-if="updateModal"
            @click.self="closeModal"
        >
            <div class="modal modal-block">
                <span class="text--default" style="font-size: 18px;margin-bottom: 10px"><b>{{ $t('update_modal__title') }}</b></span>
                <span class="text--default" style="margin-bottom: 24px">{{ $t('update_modal__text') }}</span>
                <ion-button class="btn" color="primary" size="small" expand="block"
                    @click="closeModal"
                >
                    {{ $t('okay') }}
                </ion-button>
            </div>
        </div>
    </transition>
</template>

<script>
import { IonButton } from "@ionic/vue";
import {mapGetters} from "vuex";

export default {
    name: "ModalUpdateApp",
    components: { IonButton },
    computed: {
        ...mapGetters({
            updateModal: 'common/GET_UPDATE_APP_MODAL'
        })
    },
    methods: {
        closeModal() {
            this.$store.commit('common/SET_UPDATE_APP_MODAL', false);
        }
    }
}
</script>
