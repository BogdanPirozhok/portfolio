<template>
    <ion-modal
        :is-open="show"
        :showBackdrop="false"
        @didDismiss="$emit('update:show', false)"
    >
        <default-layout
            :content-id="'people-filter'"
            :page-class="'typical--fix-btn'"
            :page-title="$t('title.user_filter')"
            :toolbar-search="true"
            :scroll-btn-top="true"
        >
            <template v-slot:searchItem>
                <div class="form__input form__input--medium search">
                    <input
                        type="search"
                        enterkeyhint="search"
                        :placeholder="$t('auth.search', { field: $t(searchPlaceholder).toLowerCase() })"
                        debounce="500"
                        v-model="search"
                        required
                    />
                    <ion-icon class="search__left" :icon="require('@/assets/icons/search-icon.svg')"/>
                    <ion-button class="btn btn--icon search__right" color="secondary" slot="start"
                        @click="search = null"
                    >
                        <ion-icon :icon="require('@/assets/icons/close-icon.svg')" style="font-size: 18px"/>
                    </ion-button>
                </div>
            </template>
            <template v-slot:headerButton>
                <ion-button class="btn btn--head btn--icon" color="secondary" slot="start"
                    @click="handleFilter"
                >
                    <ion-icon :icon="require('@/assets/icons/arrow-left-icon.svg')" style="font-size: 16px"/>
                </ion-button>
            </template>

            <tabs-swipe style="margin-top: 22px"
                :list="tabs.list"
                v-model:selected="tabs.selected"
                v-model:carrWidth="tabs.carriage.width"
                v-model:carrPosition="tabs.carriage.positionX"
            />

            <filter-country-new class="wrapper-x" style="padding-top: 8px;"
                v-if="tabs.selected === 1"
                :search="search"
                :selectedMax="5"
                :min-delete="false"
                v-model:selected="filter.countryIds"
            />
            <filter-checked style="padding-top: 8px;"
                v-if="tabs.selected === 2"
                :search="search"
                :is-load-data="!professions.length"
                :list="professions"
                v-model:selected="filter.professionIds"
            />
            <filter-checked style="padding-top: 8px;"
                v-if="tabs.selected === 3"
                :search="search"
                :is-load-data="!interests.length"
                :list="interests"
                v-model:selected="filter.interestIds"
            />
            <filter-checked style="padding-top: 8px;"
                v-if="tabs.selected === 4"
                :search="search"
                :is-load-data="!usefuls.length"
                :list="usefuls"
                v-model:selected="filter.usefulIds"
            />
            <filter-country-new class="wrapper-x" style="padding-top: 8px;"
                v-if="tabs.selected === 5"
                :search="search"
                :selectedMax="5"
                :min-delete="false"
                v-model:selected="filter.wishCountryIds"
            />
            <filter-checked style="padding-top: 8px;"
                v-if="tabs.selected === 6"
                :search="search"
                :is-load-data="!englishLevels.length"
                :list="englishLevels"
                v-model:selected="filter.englishLevelIds"
            />

            <div class="fix-bottom">
                <ion-button class="btn" color="primary" size="large" expand="block"
                    @click="handleFilter"
                >
                    {{ $t('apply') }}
                </ion-button>
            </div>
        </default-layout>
    </ion-modal>
</template>

<script>
import { IonButton, IonModal, IonIcon } from "@ionic/vue";
import TabsSwipe from "@/components/common/TabsSwipe";
import FilterChecked from "@/components/common/FilterChecked";
import FilterCountryNew from "@/components/common/FilterCountryNew";
import { mapActions, mapGetters } from "vuex";

export default {
    name: "PeopleFilter",
    components: { FilterCountryNew, FilterChecked, TabsSwipe, IonButton, IonModal, IonIcon },
    props: {
        show: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            search: null,
            tabs: {
                selected: 1,
                carriage: {
                    width: null,
                    positionX: null
                },
                list: [
                    {
                        id: 1,
                        text: 'tab.country'
                    },
                    {
                        id: 2,
                        text: 'tab.profession'
                    },
                    {
                        id: 3,
                        text: 'tab.in_need'
                    },
                    {
                        id: 4,
                        text: 'tab.i_can_useful'
                    },
                    {
                        id: 5,
                        text: 'tab.want_move'
                    },
                    {
                        id: 6,
                        text: 'auth.english_level'
                    }
                ]
            },
            filter: {
                countryIds: [],
                professionIds: [],
                interestIds: [],
                usefulIds: [],
                wishCountryIds: [],
                englishLevelIds: [],
            }
        }
    },
    mounted() {
        this.init();
        this.setSelected();
    },
    computed: {
        ...mapGetters({
            isLoadData: 'common/GET_IS_LOAD_DATA',
            englishLevels: 'reference/GET_ENGLISH_LEVELS',
            interests: 'reference/GET_INTERESTS',
            usefuls: 'reference/GET_USEFULS',
            professions: 'reference/GET_PROFESSIONS',
            params: 'user/GET_USER_SAMPLE_PARAMS',
        }),
        searchPlaceholder() {
            switch (this.tabs.selected) {
                case 1:
                    return 'auth.country_1';
                case 2:
                    return 'auth.for_profession';
                case 3:
                    return 'auth.for_what_i_need';
                case 4:
                    return 'auth.for_whats_useful';
                case 5:
                    return 'auth.for_whats_move';
                case 6:
                    return 'auth.for_english_level';
                default:
                    return '';
            }
        }
    },
    watch: {
        'tabs.selected'() {
            this.search = null;
        }
    },
    methods: {
        ...mapActions({
            fetchEnglishLevels: 'reference/fetchEnglishLevels',
            fetchProfessions: 'reference/fetchProfessions',
            fetchUsefuls: 'reference/fetchUsefuls',
            fetchInterests: 'reference/fetchInterests',
            getUserSample: 'user/getUserSample',
        }),
        handleFilter() {
            this.$emit('update:show', !this.show);

            this.$store.commit('user/SET_USER_SAMPLE_RESOURCE', '/users');
            this.$store.commit('user/SET_USER_SAMPLE_ITEMS', null);
            this.$store.commit('common/SET_IS_LOAD_GLOBAL_DATA', true);

            this.$store.commit('user/SET_USER_SAMPLE_PARAMS', {
                search: null,
                isBlocked: false,

                countryIds: this.filter.countryIds,
                usefulIds: this.filter.usefulIds,
                interestIds: this.filter.interestIds,
                professionIds: this.filter.professionIds,
                englishLevelIds: this.filter.englishLevelIds,
                wishCountryIds: this.filter.wishCountryIds,

                page: 1,
                perPage: 10
            })
            this.getUserSample();
        },
        init() {
            if (!this.englishLevels.length) {
                this.fetchEnglishLevels();
            }
            if (!this.interests.length) {
                this.fetchInterests();
            }
            if (!this.usefuls.length) {
                this.fetchUsefuls();
            }
            if (!this.professions.length) {
                this.fetchProfessions();
            }
        },
        setSelected() {
            this.filter.countryIds = [...this.params.countryIds ?? []];
            this.filter.professionIds = [...this.params.professionIds ?? []];
            this.filter.interestIds = [...this.params.interestIds ?? []];
            this.filter.usefulIds = [...this.params.usefulIds ?? []];
            this.filter.wishCountryIds = [...this.params.wishCountryIds ?? []];
            this.filter.englishLevelIds = [...this.params.englishLevelIds ?? []];
        }
    }
}
</script>
