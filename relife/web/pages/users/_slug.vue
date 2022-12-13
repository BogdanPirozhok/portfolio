<template>
    <main class="footer--white">
        <section class="profile-user">
            <div class="wrapper anim anim-up">
                <div
                    v-if="account"
                    class="profile-user__box"
                >
                    <div class="profile-user__avatar">
                        <img
                            v-if="account.profile.avatar"
                            crossorigin="anonymous"
                            class="main-img"
                            :src="account.profile.avatar"
                            alt="avatar"
                        >
                        <svg
                            v-else
                            width="48"
                            height="48"
                            viewBox="0 0 48 48"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M24 30.3479C32.6773 30.3479 40 31.7579 40 37.1979C40 42.64 32.6292 44 24 44C15.3247 44 8 42.59 8 37.1499C8 31.7079 15.3708 30.3479 24 30.3479ZM24 4C29.8782 4 34.588 8.70805 34.588 14.5821C34.588 20.4562 29.8782 25.1662 24 25.1662C18.1238 25.1662 13.412 20.4562 13.412 14.5821C13.412 8.70805 18.1238 4 24 4Z" />
                        </svg>
                        <img
                            v-if="account.profile.country.code"
                            crossorigin="anonymous"
                            class="country"
                            :src="require('~/assets/icons/flags-mini/' + account.profile.country.code + '.jpg')"
                            alt="country"
                        >
                    </div>

                    <div class="profile-user__group">
                        <div class="profile-user__info-box">
                            <span>{{ bigNumberFormat(account.followers_count) }}</span>
                            <p>{{ $t('profile_user__info_1') }}</p>
                        </div>
                        <div class="profile-user__info-box">
                            <span>{{ daysDuration(account.count_days_registration) }}</span>
                            <p>{{ $t('profile_user__info_2') }}</p>
                        </div>
                        <div class="profile-user__info-box">
                            <span>{{ bigNumberFormat(account.common_rating) }}</span>
                            <p>{{ $t('profile_user__info_3') }}</p>
                        </div>
                    </div>

                    <h2 class="profile-user__title">
                        {{ account.profile.nickname }}
                    </h2>

                    <span
                        v-if="account.profile.short_description"
                        class="profile-user__desc"
                        v-html="textLinkFormatter(account.profile.short_description)"
                    />

                    <template v-if="!linkApp">
                        <btn-store-group
                            :stores="stores"
                        />
                    </template>
                    <template v-else>
                        <div class="profile-user__btn-group">
                            <a
                                :href="linkApp"
                                class="btn-app"
                                style="min-width: 240px"
                                @click.prevent="goToApp"
                            >
                                <template v-if="$device.isIos">
                                    <img
                                        src="~/assets/icons/apple-stroe.svg"
                                        alt="icon"
                                        class="btn-app__icon"
                                    >
                                </template>
                                <template v-else>
                                    <img
                                        src="~/assets/icons/google-play.svg"
                                        alt="icon"
                                        class="btn-app__icon"
                                    >
                                </template>

                                <span class="btn-app__text">{{ $t(pending ? 'loading' : 'open_in_app') }}</span>
                            </a>

                            <span class="profile-user__btn-info">* {{ $t('store_info') }} <b>{{ $device.isIos ? 'AppStore' : 'GooglePlay' }}</b></span>
                        </div>
                    </template>
                </div>
            </div>
        </section>
    </main>
</template>

<script>
import BtnStoreGroup from '@/components/Common/BtnStoreGroup.vue';
import ShareMixin from '@/mixins/share';
import { textLinkFormatter } from '~/helpers/links';

export default {
    name: 'UserPage',
    components: { BtnStoreGroup },
    mixins: [ShareMixin],
    layout: 'homeLayout',
    async asyncData({ params, $axios, $nuxt }) {
        const { slug } = params;

        if (!slug) {
            $nuxt.error({ statusCode: 404 });
        }

        try {
            const { response } = await $axios.get(`/public/users/${slug}`);

            return {
                account: response.data
            };
        } catch (e) {
            $nuxt.error({ statusCode: 404 });
        }

        return null;
    },
    data() {
        return {
            account: null
        };
    },
    head() {
        return {
            title: `ReLife - @${this.account.profile.nickname}`,
            meta: [
                { hid: 'og:title', property: 'og:title', content: `ReLife - @${this.account.profile.nickname}` },
                { hid: 'twitter:title', property: 'twitter:title', content: `ReLife - @${this.account.profile.nickname}` },

                { hid: 'description', property: 'description', content: this.account.profile.short_description },
                { hid: 'og:description', property: 'og:description', content: this.account.profile.short_description },
                { hid: 'twitter:description', property: 'twitter:description', content: this.account.profile.short_description }
            ]
        };
    },
    computed: {
        pathApp() {
            return `users/${this.$route.params.slug}`;
        }
    },
    methods: {
        textLinkFormatter
    }
};
</script>
