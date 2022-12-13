<template>
    <main class="footer--white">
        <section class="post">
            <div class="wrapper anim anim-up">
                <div class="post__top">
                    <div class="post-categories">
                        <nuxt-link
                            class="post-avatar"
                            :to="'/users/' + post.author.profile.nickname"
                        >
                            <img
                                v-if="post.author.profile.avatar"
                                crossorigin="anonymous"
                                class="main-img"
                                :src="post.author.profile.avatar"
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
                                v-if="post.author.profile.country.code"
                                crossorigin="anonymous"
                                class="country"
                                :src="require('~/assets/icons/flags-mini/' + post.author.profile.country.code + '.jpg')"
                                alt="country"
                            >
                        </nuxt-link>
                        <div class="post-categories__group">
                            <nuxt-link
                                class="post-categories__name"
                                :to="'/users/' + post.author.profile.nickname"
                            >
                                {{ post.author.profile.nickname }}
                            </nuxt-link>
                            <span class="post-categories__date">{{ post.viewed_at }}</span>
                        </div>
                    </div>
                    <template v-if="!linkApp">
                        <btn-store-group
                            style="min-width: 312px;"
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
                        </div>
                    </template>
                </div>
                <div class="post__middle">
                    <h1>{{ post.title }}</h1>
                    <img
                        crossorigin="anonymous"
                        class="main-img"
                        :src="post.image"
                        alt="post images"
                    >
                    <p
                        style="white-space: pre-wrap;"
                        v-html="textLinkFormatter(post.content)"
                    />
                </div>
                <div class="post__bottom">
                    <nuxt-link
                        class="post-author"
                        :to="'/users/' + post.author.profile.nickname"
                    >
                        <div class="post-avatar">
                            <img
                                v-if="post.author.profile.avatar"
                                crossorigin="anonymous"
                                class="main-img"
                                :src="post.author.profile.avatar"
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
                                v-if="post.author.profile.country.code"
                                crossorigin="anonymous"
                                class="country"
                                :src="require('~/assets/icons/flags-mini/' + post.author.profile.country.code + '.jpg')"
                                alt="country"
                            >
                        </div>
                        <div class="post-author__group">
                            <span class="post-author__name">{{ post.author.profile.nickname }}</span>
                            <div class="post-author__bottom">
                                <div class="post-author__position">
                                    <span
                                        :style="{'background-color': '#944bba'}"
                                    >{{ post.author.profile.profession.name }}</span>
                                </div>
                            </div>
                        </div>
                        <span class="karma-square">{{ bigNumberFormat(post.author.ratings_count) }}<b>{{ $t('carma') }}</b></span>
                    </nuxt-link>
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
    name: 'PostPage',
    components: { BtnStoreGroup },
    mixins: [ShareMixin],
    async asyncData({ params, $axios, $nuxt }) {
        const { slug } = params;

        if (!slug) {
            $nuxt.error({ statusCode: 404 });
        }

        try {
            const { response } = await $axios.get(`/public/posts/${slug}`);

            return {
                post: response.data
            };
        } catch (e) {
            $nuxt.error({ statusCode: 404 });
        }

        return null;
    },
    data() {
        return {
            post: null
        };
    },
    head() {
        return {
            title: `ReLife - ${this.post.title}`,
            meta: [
                { hid: 'og:title', property: 'og:title', content: `${this.post.title}` },
                { hid: 'twitter:title', property: 'twitter:title', content: `${this.post.brief_description}` },

                { hid: 'description', property: 'description', content: this.post.brief_description },
                { hid: 'og:description', property: 'og:description', content: this.post.brief_description },
                { hid: 'twitter:description', property: 'twitter:description', content: this.post.brief_description },

                { hid: 'og:image', property: 'og:image', content: this.post.image },
                { hid: 'twitter:image', property: 'twitter:image', content: this.post.image }
            ]
        };
    },
    computed: {
        pathApp() {
            return `posts/${this.$route.params.slug}`;
        }
    },
    methods: {
        textLinkFormatter
    }
};
</script>
