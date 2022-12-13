<template>
    <transition name="overlay">
        <div class="overlay"
            v-if="show"
            @click.self="closeModal"
        >
            <div class="modal modal-block">
                <span class="text--default" style="font-size: 18px;margin-bottom: 10px;"
                    v-if="title"
                ><b>{{ title }}</b></span>
                <span class="text--default" style="margin-bottom: 24px;"
                    v-if="text"
                >{{ text }}</span>
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

export default {
    name: "ModalPrimary",
    components: { IonButton },
    props: {
        show: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: null
        },
        text: {
            type: String,
            default: null
        }
    },
    methods: {
        closeModal() {
            this.$emit('update:show', false);
        }
    }
}
</script>
