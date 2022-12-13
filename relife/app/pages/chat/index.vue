<template>
    <default-layout
        :page-title="$t('title.chat')"
        :content-id="'main-chat-content'"
        :scroll-btn-top="true"
        style="padding-bottom: 32px"
    >
        <template v-slot:headerButton>
            <ion-button class="btn btn--head btn--icon" color="secondary" slot="start"
                @click="$router.back()"
            >
                <ion-icon :icon="require('@/assets/icons/arrow-left-icon.svg')" style="font-size: 16px"/>
            </ion-button>
            <!--<ion-button class="btn btn--head btn--icon" color="secondary" slot="end">
                <ion-icon :icon="require('@/assets/icons/search-icon.svg')"/>
            </ion-button>-->
        </template>

        <ion-refresher slot="fixed" @ionRefresh="doRefresh">
            <ion-refresher-content />
        </ion-refresher>

        <div class="chat-list">
            <template v-if="!chats?.data">
                <div class="chat-list__box"
                    v-for="item in 4"
                    :key="item"
                >
                    <div class="people-box__img" style="background-color: transparent">
                        <ion-skeleton-text animated style="--border-radius: 10px" />
                    </div>
                    <div class="chat-list__group">
                        <div class="chat-list__top">
                            <ion-skeleton-text animated style="height: 18px;width: 70%" />
                        </div>
                        <div class="chat-list__middle">
                            <ion-skeleton-text animated style="height: 14px;width: 100%" />
                        </div>
                    </div>
                </div>
            </template>

            <template v-else>
                <template v-if="chats?.data && chats.data.length">
                    <ion-item-sliding class="sliding"
                        v-for="item in chats.data"
                        :key="item"
                    >
                        <ion-item @click="toChat(item.id, item.user.profile)">
                            <div class="chat-list__box ion-activatable ripple-parent">
                                <div class="people-box__img">
                                    <ion-img v-if="item.user.profile.avatar" class="main-img" :src="item.user.profile.avatar"/>
                                    <ion-icon v-else class="icon" :icon="require('@/assets/icons/avatar-icon.svg')"/>
                                    <ion-img v-if="item.user.profile?.country" class="flag" :src="require('@/assets/icons/flags/' + item.user.profile.country.code + '.jpg')"/>
                                    <!-- <span v-if="item.online" class="badge badge-online" />-->
                                </div>
                                <div class="chat-list__group">
                                    <div class="chat-list__top">
                                        <span class="chat-list__name">{{ item.user.profile.nickname }}</span>
                                        <template v-if="item.message">
                                            <!-- <ion-icon v-if="!item.message.is_read" class="chat-list__mark" :icon="require('@/assets/icons/mark-chat&#45;&#45;one.svg')"/>-->
                                            <ion-icon v-if="item.message.is_read" class="chat-list__mark" :icon="require('@/assets/icons/mark-chat--two.svg')"/>
                                            <span class="chat-list__date">{{ formatDate(item.message.sent_at) }}</span>
                                        </template>
                                    </div>
                                    <div v-if="item.message" class="chat-list__middle">
                                        <span class="chat-list__text">{{ item.message.text }}</span>
                                        <span v-if="!item.message.is_read" class="chat-list__unread">1+</span>
                                    </div>
                                </div>
                                <ion-ripple-effect />
                            </div>
                        </ion-item>

                        <ion-item-options side="end">
                            <ion-item-option @click="handleDeleteChat(item.id)" color="danger">
                                <ion-icon :icon="require('@/assets/icons/delete-icon.svg')"/>
                            </ion-item-option>
                        </ion-item-options>
                    </ion-item-sliding>
                </template>

                <template v-else>
                    <no-data
                        :title="'comSoon.no_data__chat'"
                    />
                </template>
            </template>
        </div>
        <ion-infinite-scroll
            @ionInfinite="onIonInfinite"
            :disabled="isDisableInfiniteScroll"
        >
            <ion-infinite-scroll-content>
                <loader-item :stroke-width="5" />
            </ion-infinite-scroll-content>
        </ion-infinite-scroll>
    </default-layout>
</template>

<script>
import {
    IonButton,
    IonIcon,
    IonImg,
    IonRippleEffect,
    IonItemSliding,
    IonItemOptions,
    IonItemOption,
    IonItem,
    IonSkeletonText,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
    IonRefresher,
    IonRefresherContent, alertController,
} from '@ionic/vue'
import NoData from "@/components/common/NoData";
import { mapActions, mapGetters } from 'vuex'
import LoaderItem from "@/components/common/Loader";
import { formatDate } from '@/helpers/date';

export default {
    name: "ChatMainPage",
    components: {
        NoData,
        LoaderItem,
        IonButton,
        IonIcon,
        IonImg,
        IonRippleEffect,
        IonItemSliding,
        IonItemOptions,
        IonItemOption,
        IonItem,
        IonSkeletonText,
        IonInfiniteScroll,
        IonInfiniteScrollContent,
        IonRefresher,
        IonRefresherContent,
    },
    data() {
        return {
            isLoadData: false,
        }
    },
    computed: {
        ...mapGetters({
            chats: 'chat/GET_CHATS'
        }),
        isDisableInfiniteScroll() {
            return this.chats?.meta.current_page === this.chats?.meta.last_page
        },
    },
    mounted () {
        this.fetchChats();
    },
    methods: {
        formatDate,
        ...mapActions({
            fetchChats: 'chat/fetchChats',
            deleteChat: 'chat/deleteChat',
        }),
        toChat(id, user) {
            this.$router.push({
                name: 'user-chat',
                params: {
                    chatId: id,
                    user: JSON.stringify(user)
                }
            })
        },
        onIonInfinite(event) {
            this.$store.commit('chat/SET_PARAM_PAGE', 1);

            this.fetchChats();
            event.target.complete()
        },
        doRefresh(event) {
            this.fetchChats().then(() => {
                event.target.complete();
            });
        },
        async handleDeleteChat(id) {
            const alert = await alertController.create({
                header: this.$t('delete_chat__title'),
                message: this.$t('delete_chat__text'),
                cssClass: 'с-alert',
                buttons: [
                    {
                        text: this.$t('cancel'),
                        cssClass: 'с-alert__btn-confirm'
                    },
                    {
                        text: this.$t('delete'),
                        cssClass: 'с-alert__btn-cancel',
                        handler: () => {
                            this.deleteChat(id).then(() => {
                                this.fetchChats();
                            })
                        }
                    },
                ],
            });

            await alert.present();
        },
    }
}
</script>
