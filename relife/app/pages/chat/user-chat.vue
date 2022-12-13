<template>
    <ion-page class="typical">
        <ion-header>
            <ion-toolbar>
                <ion-button class="btn btn--head btn--icon" color="secondary" slot="start"
                    @click="$router.back()"
                >
                    <ion-icon :icon="require('@/assets/icons/arrow-left-icon.svg')" style="font-size: 16px"/>
                </ion-button>
                <ion-title style="padding-right: 0">
                    <div class="chat-user"
                        v-if="companionProfile"
                        @click="toUserProfile"
                    >
                        <div class="people-box__img">
                            <ion-img v-if="companionProfile?.avatar" class="main-img" :src="companionProfile?.avatar"/>
                            <ion-icon v-else class="icon" :icon="require('@/assets/icons/avatar-icon.svg')"/>
                            <ion-img v-if="companionProfile?.country" class="flag" :src="require('@/assets/icons/flags/' + companionProfile?.country.code + '.jpg')"/>
                        </div>
                        <div class="chat-list__group"
                            :style="{'padding-left': !companionProfile?.country ? '10px' : '16px'}"
                        >
                            <div class="chat-list__top">
                                <span class="chat-list__name">{{ companionProfile?.nickname }}</span>
                            </div>
                            <div class="chat-list__middle">
                                <span  class="chat-list__text"
                                    :style="{color: list.online ? 'var(--ion-color-primary)' : 'var(--text-secondary)'}"
                                >{{ companionProfile?.first_name + ' ' + companionProfile?.last_name }}</span>
                            </div>
                        </div>
                    </div>
                </ion-title>
            </ion-toolbar>
            <div class="ion-header__bg ion-header__bg--chat" />
        </ion-header>
        <ion-content :id="'chat-content-' + chatId"
            :fullscreen="true"
            :scrollEvents="true"
            @ionScroll="handleScroll"
        >
            <ion-infinite-scroll
                threshold="25%"
                position="top"
                @ionInfinite="onIonInfinite"
                :disabled="isDisableInfiniteScroll"
            >
                <ion-infinite-scroll-content>
                    <loader-item :stroke-width="5" />
                </ion-infinite-scroll-content>
            </ion-infinite-scroll>
            <div class="chat">
                <div class="chat__container wrapper-x">
                    <template v-if="messages?.data">
                        <!--<span class="chat__date">27 Августа</span>-->
                        <div class="chat-message"
                            v-for="(item, index) in messages.data"
                            :key="item.id ?? 'unsent_message' + index"
                            :class="{left: item.user.id !== user?.id, right: item.user.id === user?.id, active: selectedMessage === item.id}"
                            :style="{filter: sheet ? 'blur(2px)' : 'blur(0)'}"
                            @click="(item.user.id !== user?.id && item.id) || handleMessage(item.id)"
                        >
                            <span class="chat-message__text" v-html="textLinkFormatter(item.text)" />
                            <div class="chat-message__bottom">
                                <span class="chat-message__time">{{ formatDateTime(item.sent_at) }}</span>
                                <ion-icon
                                    v-if="!item?.id && error"
                                    class="chat-message__mark"
                                    :icon="require('@/assets/icons/info-circle-icon.svg')"
                                    style="color: var(--ion-color-danger);opacity: .8"/>
                                <template v-if="item.user.id === user?.id">
                                    <ion-icon v-if="!item.is_read" class="chat-message__mark" :icon="require('@/assets/icons/mark-chat--one.svg')"/>
                                    <ion-icon v-if="item.is_read" class="chat-message__mark" :icon="require('@/assets/icons/mark-chat--two.svg')"/>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="comment-add">
                    <div
                        class="scroll-top"
                        :class="{active: topActive}"
                        @click="scrollToBottom"
                    >
                        <ion-button class="btn btn--icon" color="secondary">
                            <ion-icon slot="icon-only" :icon="require('@/assets/icons/down-arrow-icon.svg')"/>
                        </ion-button>
                    </div>
                    <label class="comment-add__box">
                        <ion-textarea
                            :autoGrow="true"
                            :rows="1"
                            v-model="message"
                            :placeholder="$t('message')"
                        />
                        <ion-button
                            @click="handleSendMessage"
                            :disabled="isLoading"
                            class="btn btn--icon"
                            color="tertiary"
                            slot="end"
                            :class="{'btn--send': message.length }"
                            :style="{'pointer-events': !message.length ? 'none' : 'auto'}"
                        >
                            <ion-icon :icon="require('@/assets/icons/send-icon.svg')" style="font-size: 22px"/>
                        </ion-button>
                    </label>
                </div>
            </div>

            <ion-modal class="modal-sheet modal-sheet--list" style="--height: 112px"
                :is-open="sheet"
                :initial-breakpoint="1"
                :breakpoints="[0, 1]"
                @did-dismiss="sheet = false"
            >
                <ion-content class="wrapper-sheet" :fullscreen="true">
                    <div class="select-list">
                        <ion-card @click="handleDelete" :disabled="isLoading" class="select-list__item ion-activatable ripple-parent">
                            <ion-icon class="select-list__icon--start" :icon="require('@/assets/icons/delete-icon.svg')" style="font-size: 22px;"/>
                            {{ $t('delete') }}
                            <ion-ripple-effect />
                        </ion-card>
                    </div>
                </ion-content>
            </ion-modal>
        </ion-content>
    </ion-page>
</template>

<script>
import {
    IonButton,
    IonContent,
    IonHeader,
    IonIcon,
    IonPage,
    IonTitle,
    IonToolbar,
    IonImg,
    IonTextarea,
    IonModal,
    IonCard,
    IonRippleEffect,
    IonInfiniteScroll,
    IonInfiniteScrollContent,
} from "@ionic/vue";
import { mapActions, mapGetters } from 'vuex'
import { formatDateTime } from '@/helpers/date';
import LoaderItem from '@/components/common/Loader.vue';
import { textLinkFormatter } from '@/helpers/common';

export default {
    name: "UserChatPage",
    components: {
        LoaderItem,
        IonButton,
        IonIcon,
        IonContent,
        IonHeader,
        IonToolbar,
        IonTitle,
        IonPage,
        IonImg,
        IonTextarea,
        IonModal,
        IonCard,
        IonRippleEffect,
        IonInfiniteScroll,
        IonInfiniteScrollContent,
    },
    data() {
        return {
            opacity: 0,
            message: '',
            sheet: false,
            list: {
                id: 1,
                avatar: 'avatar-2.jpg',
                country: 'cyp.jpg',
                name: 'Александр Майборода',
                lastMessage: '8-дневный тур из Лхасы в базовый лагерь Эвереста — один из самых популярных среди наших посетителей.',
                date: '12 авг.',
                unread: 0,
                read: false,
                online: true
            },
            selectedMessage: null,
            topActive: false,
            error: false,
            isLoading: false,
        }
    },
    computed: {
        ...mapGetters({
            messages: 'chat/GET_MESSAGES',
            user: 'user/GET_USER',
            currentChat: 'chat/GET_CURRENT_CHAT'
        }),
        statusUser() {
            return this.list.online ? 'online' : 'offline';
        },
        chatId() {
            return Number(this.$route.params.chatId);
        },
        chatUser() {
            return this.messages?.data.filter((item) => item.user.id !== this.user?.id)[0]?.user ?? this.currentChat;
        },
        isDisableInfiniteScroll() {
            return this.messages?.meta.current_page === this.messages?.meta.last_page
        },
        companionProfile() {
            return this.$route.params.user ? JSON.parse(this.$route.params.user) : this.chatUser?.profile;
        }
    },
    mounted() {
        this.$store.commit('chat/SET_MESSAGES', null);

        this.fetchCurrentChat(this.chatId);

        this.fetchChatMessages(this.chatId).then(() => {
            this.scrollToBottom();

            setTimeout(() => {
                if (this.chatId) {
                    this.setRead(this.chatId);
                }
            }, 2000);
        });
    },
    methods: {
        formatDateTime,
        textLinkFormatter,
        ...mapActions({
            fetchChatMessages: 'chat/fetchChatMessages',
            fetchCurrentChat: 'chat/fetchCurrentChat',
            deleteMessage: 'chat/deleteMessage',
            sendMessage: 'chat/sendMessage',
            setRead: 'chat/setRead',
        }),
        handleScroll(e) {
            const scrollTop = e.detail.scrollTop;

            const maxVal = 46;
            const scrollOpacity = scrollTop > maxVal ? maxVal : scrollTop;
            this.opacity = (maxVal - (maxVal - scrollOpacity)) / maxVal;

            const deviceHeight = e.target.scrollEl.clientHeight;
            const scrollFullHeight = e.target.scrollEl.scrollHeight;
            this.topActive = scrollTop < scrollFullHeight - deviceHeight - 700;
        },
        scrollToBottom() {
            document.getElementById('chat-content-' + this.chatId).scrollToBottom(this.topActive ? 500 : 0);
        },
        handleMessage(id)  {
            this.sheet = true;
            this.selectedMessage = id;
        },
        handleDelete() {
            const oldMessages = JSON.parse(JSON.stringify(this.messages.data));
            this.messages.data = this.messages.data.filter((item) => item.id !== this.selectedMessage);

            this.sheet = false;

            this.isLoading = true;
            this.deleteMessage(this.selectedMessage)
                .catch(() => {
                    this.messages.data = oldMessages;
                }).finally(() => {
                    this.isLoading = false
                })
        },
        handleSendMessage() {
            const date = new Date();

            const data = {
                text: this.message,
                timestamp: date.toISOString()
            };

            this.messages.data.push({
                text: data.text,
                is_read:false,
                user: this.user,
                sent_at: data.timestamp,
                created_at: data.timestamp,
                updated_at: data.timestamp,
            })

            this.scrollToBottom();

            this.message = '';

            this.isLoading = true;
            this.sendMessage({
                chatId: this.chatId,
                data
            }).then((res) => {
                this.messages.data.forEach((item) => {
                    const sentDateISO = item.sent_at.split('.')[0] + 'Z';
                    const resDateISO = res.data.sent_at.split('.')[0] + 'Z';

                    const sentDateValueOf = new Date(sentDateISO).valueOf();
                    const resDateValueOf = new Date(resDateISO).valueOf();

                    if (sentDateValueOf === resDateValueOf) {
                        item.id = res.data.id;
                    }
                });
            }).catch(() => {
                this.error = true;
            }).finally(() => {
                this.isLoading = false;
            })
        },
        onIonInfinite(event) {
            this.$store.commit('chat/SET_MESSAGES_PARAM_PAGE', 1);

            this.fetchChatMessages(this.chatId);
            event.target.complete()
        },
        toUserProfile() {
            if (this.chatUser?.id) {
                this.$router.push({
                    name: 'user-profile',
                    params: {
                        user_id: this.chatUser.id
                    }
                })
            }
        }
    }
}
</script>
