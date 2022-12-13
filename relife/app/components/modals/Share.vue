<template>
    <ion-modal
        class="modal-sheet modal-sheet--list modal-sheet--article"
        :style="{'--height': '160px'}"
        :is-open="isOpen"
        :initial-breakpoint="1"
        :breakpoints="[0, 1]"
        @did-dismiss="isOpen = false"
    >
        <ion-content class="wrapper-sheet" :fullscreen="true">
            <div class="select-list">
                <ion-card class="select-list__item ion-activatable ripple-parent"
                    @click="shareContent"
                >
                    <ion-icon class="select-list__icon--start" :icon="require('@/assets/icons/share-icon.svg')" style="font-size: 18px;"/>
                    {{ $t('title.share') }}
                    <ion-ripple-effect />
                </ion-card>
                <ion-card class="select-list__item ion-activatable ripple-parent"
                    @click="shareLink"
                >
                    <ion-icon class="select-list__icon--start" :icon="require('@/assets/icons/copy-icon.svg')" style="font-size: 18px;"/>
                    {{ link ? $t('title.link_do_copy') : $t('title.value_do_copy') }}
                    <ion-ripple-effect />
                </ion-card>
            </div>
        </ion-content>
    </ion-modal>
</template>

<script>
import doCopy from "@/helpers/do-copy";
import { shareHost } from "@/helpers/url";
import { Share } from '@capacitor/share';

export default {
    name: "ModalShare",
    props: {
        link: {
            type: String,
            default: ''
        },
        value: {
            type: String,
            default: ''
        },
        title: {
            type: String,
            require: true,
            default: ''
        },
        text: {
            type: String,
            require: true,
            default: ''
        },
        logData: {
            type: Object,
            require: true,
            default: null
        }
    },
    data() {
        return {
            isOpen: false
        }
    },
    methods: {
        doCopy, shareHost,
        show(isOpen = true) {
            this.isOpen = isOpen;
        },
        shareContent() {
            Share.share({
                title: this.title,
                text: this.text + ' ' + this.shareHost(this.link),
                dialogTitle: this.title,
            });

            this.logEvent('share', {
                content_type: this.logData.type,
                item_id: this.logData.id,
            })
        },
        shareLink() {
            this.doCopy({
                value: this.link ? this.shareHost(this.link) : this.value,
                message: this.link ? this.$t('title.link_copied') : this.$t('title.value_copied')
            });

            this.logEvent('share', {
                content_type: 'link_' + this.logData.type,
                item_id: this.logData.id,
            })
        }
    }
}
</script>
