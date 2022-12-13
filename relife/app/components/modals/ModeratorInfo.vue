<template>
    <transition name="overlay">
        <template v-if="moderatorInfo">
            <div class="overlay"
                @click.self="closeModal"
            >
                <div class="modal modal-block">
                    <span class="text--default" style="font-size: 18px;margin-bottom: 10px"><b>{{ $t('title.suggest_changes') }}</b></span>
                    <span class="text--default" style="margin-bottom: 24px;color: var(--text-secondary)">
                        {{ $t('moderator_info_text') }}
                    </span>
                    <span class="text--small">
                        <ion-icon
                            :icon="require('@/assets/icons/' + getIcon(moderatorInfo.posts_karma, 50))"
                            :class="{success: moderatorInfo.posts_karma >= 50}"
                        />
                        {{ $t('moderator_info_posts_karma') }} <b>{{ moderatorInfo.posts_karma }} / 50</b>
                    </span>
                    <span class="text--small">
                        <ion-icon
                            :icon="require('@/assets/icons/' + getIcon(moderatorInfo.posts_count, 3))"
                            :class="{success: moderatorInfo.posts_count >= 3}"
                        />
                        {{ $t('moderator_info_posts_count') }} <b>{{ moderatorInfo.posts_count }} / 3</b>
                    </span>
                    <span class="text--small">
                        <ion-icon
                            :icon="require('@/assets/icons/' + getIcon(moderatorInfo.days_passed, 7))"
                            :class="{success: moderatorInfo.days_passed >= 7}"
                        />
                        {{ $t('moderator_info_days_together') }} <b>{{ moderatorInfo.days_passed }} / 7</b>
                    </span>
                    <span class="text--small">
                        <ion-icon
                            :icon="require('@/assets/icons/' + getIconBoolean(!!moderatorInfo.avatar))"
                            :class="{success: !!moderatorInfo.avatar}"
                        />
                        {{ $t('moderator_info_avatar') }}
                    </span>
                    <span class="text--small">
                        <ion-icon
                            :icon="require('@/assets/icons/' + getIconBoolean(!!moderatorInfo.short_description))"
                            :class="{success: !!moderatorInfo.short_description}"
                        />
                        {{ $t('moderator_info_short_description') }}
                    </span>
                    <span class="text--small" style="margin-bottom: 32px;">
                        <ion-icon
                            :icon="require('@/assets/icons/' + getIconBoolean(!!moderatorInfo.full_description))"
                            :class="{success: !!moderatorInfo.full_description}"
                        />
                        {{ $t('moderator_info_full_description') }}
                    </span>
                    <ion-button class="btn" color="primary" size="small" expand="block"
                        @click="closeModal"
                    >
                        {{ $t('okay') }}
                    </ion-button>
                </div>
            </div>
        </template>
    </transition>
</template>

<script>
import { IonButton, IonIcon } from "@ionic/vue";
import { mapGetters } from 'vuex'

export default {
    name: "ModalModeratorInfo",
    components: { IonButton, IonIcon },
    computed: {
        ...mapGetters({
            user: 'user/GET_USER',
            moderatorInfo: 'common/GET_MODERATOR_INFO',
        }),
    },
    methods: {
        closeModal() {
            this.$store.commit('common/SET_MODERATOR_INFO', null);
        },
        getIcon(value, number) {
            return value >= number ? 'checkmark-icon.svg' : 'close-icon--wrap.svg';
        },
        getIconBoolean(value) {
            return value ? 'checkmark-icon.svg' : 'close-icon--wrap.svg';
        }
    }
}
</script>

<style scoped lang="scss">
    .text--small {
        margin-bottom: 8px;
        width: 100%;
        text-align: left;
        position: relative;
        padding-left: 26px;
        ion-icon {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: var(--ion-color-danger);
            &.success {
                color: var(--ion-color-success);
                font-size: 14px;
                padding: 0 2px;
            }
        }
        b {
            white-space: nowrap;
        }
    }
</style>
