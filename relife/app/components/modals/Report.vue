<template>
    <transition name="overlay">
        <template
            v-if="reportModal">
            <div class="overlay"
                @click.self="closeModal"
            >
                <form @submit.prevent="sendReport" method="post" class="modal modal-block modal-report">
                    <span class="text--default"><b>{{ $t('report') }}</b></span>

                    <div class="form__box">
                        <span class="form__label">
                            {{ $t('auth.enter', { field: $t('auth.your_report') }) }} <b>{{ model.report.length }}/1000</b>
                        </span>
                        <div class="form__textarea"
                            :class="{ error: v$.model.report.$error }"
                        >
                            <ion-textarea class="no-scroll"
                                :autoGrow="false"
                                :rows="8"
                                :enterkeyhint="'done'"
                                :maxlength="1500"
                                v-model="model.report"
                            />
                            <span class="text--error">{{ v$.model.report.$errors[0]?.$message }}</span>
                        </div>
                    </div>

                    <div class="modal-block__group-btn">
                        <ion-button class="btn" color="secondary" size="small" expand="block"
                            @click="closeModal"
                        >
                            {{ $t('close') }}
                        </ion-button>
                        <ion-button type="submit" class="btn" color="primary" size="small" expand="block">
                            {{ $t('send') }}
                        </ion-button>
                    </div>
                </form>
            </div>
        </template>
    </transition>
</template>

<script>
import { IonButton, IonTextarea } from "@ionic/vue";
import { mapActions, mapGetters } from 'vuex'
import {maxLength, minLength, required} from "@/helpers/i18n-validators";
import useVuelidate from "@vuelidate/core";

export default {
    name: "ModalReport",
    components: { IonButton, IonTextarea },
    data() {
        return {
            model: {
                report: '',
            },
        }
    },
    validations: {
        model: {
            report: {
                required,
                minLength: minLength(3),
                maxLength: maxLength(1500)
            }
        },
    },
    computed: {
        ...mapGetters({
            reportModal: 'common/GET_REPORT_MODAL',
            reportTarget: 'common/GET_REPORT_TARGET',
        })
    },
    setup() {
        return { v$: useVuelidate() }
    },
    methods: {
        ...mapActions({
            userReport: 'user/sendReport',
            postReport: 'feed/sendReport',
        }),
        closeModal() {
            this.$store.commit('common/SET_REPORT_MODAL', false);
            this.$store.commit('common/SET_REPORT_TARGET', {
                id: null,
                tax: null
            });
        },
        sendReport() {
            switch (this.reportTarget.tax) {
                case 'user':
                    this.userReport({
                        id: this.reportTarget.id,
                        text: this.model.report
                    }).finally(() => {
                        this.closeModal();
                    });
                    break;
                case 'post':
                    this.postReport({
                        id: this.reportTarget.id,
                        text: this.model.report
                    }).finally(() => {
                        this.closeModal();
                    });
                    break;
            }
        }
    }
}
</script>
