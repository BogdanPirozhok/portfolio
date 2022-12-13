import axios from '../../helpers/axios';
import router from '../../router'

export default{
    namespaced: true,
    state: {
        chats: null,
        current_chat: null,
        messages: null,
        chats_params: {
            page: 1,
        },
        messages_params: {
            page: 1,
        },
        has_new_messages: false
    },
    getters: {
        GET_CHATS: (state) => state.chats,
        GET_CURRENT_CHAT: (state) => state.current_chat,
        GET_MESSAGES: (state) => state.messages,
        GET_CHATS_PARAMS: (state) => state.chats_params,
        GET_MESSAGES_PARAMS: (state) => state.messages_params,
        GET_HAS_NEW_MESSAGES: (state) => state.has_new_messages,
    },
    mutations: {
        SET_CHATS(state, payload) {
            state.chats = payload;
        },
        SET_CURRENT_CHAT(state, payload) {
            state.current_chat = payload;
        },
        SET_MESSAGES(state, payload) {
            state.messages = payload;
        },
        SET_PARAM_PAGE(state, payload) {
            if (payload) {
                state.chats_params.page += payload;
            } else {
                state.chats_params.page = 1;
            }
        },
        SET_MESSAGES_PARAM_PAGE(state, payload) {
            if (payload) {
                state.messages_params.page += payload;
            } else {
                state.messages_params.page = 1;
            }
        },
        SET_HAS_NEW_MESSAGES(state, payload) {
            state.has_new_messages = payload;
        },
    },
    actions: {
        fetchChats({commit, getters}) {
            commit('common/SET_IS_LOAD_GLOBAL_DATA', true, { root: true });
            const params = getters.GET_CHATS_PARAMS;

            return axios({
                method: 'get',
                url: '/chats/',
                version: 2,
                params
            }).then((res) => {
                if (params.page > 1) {
                    const currentChats = getters.GET_CHATS;

                    currentChats.data = [...currentChats.data, ...res.data];
                    currentChats.meta = res.meta;
                    currentChats.links = res.links;

                    commit('SET_CHATS', currentChats);
                } else {
                    commit('SET_CHATS', res);
                }
            }).finally(() => {
                commit('common/SET_IS_LOAD_GLOBAL_DATA', false, { root: true });
            })
        },
        fetchCurrentChat({commit, getters}, id) {
            commit('common/SET_IS_LOAD_GLOBAL_DATA', true, { root: true });
            const params = getters.GET_CHATS_PARAMS;

            return axios({
                method: 'get',
                url: '/chats/' + id,
                params
            }).then((res) => {
                commit('SET_CURRENT_CHAT', res.data);
            }).finally(() => {
                commit('common/SET_IS_LOAD_GLOBAL_DATA', false, { root: true });
            })
        },
        // eslint-disable-next-line no-empty-pattern
        createOrGetChat({}, user) {
            const data = {
                user_id: user.id
            };

            return axios({
                method: 'post',
                url: '/chats',
                data
            }).then((res) => {
                router.push({
                    name: 'user-chat',
                    params: {
                        chatId: res.data.id,
                        user: JSON.stringify(user.profile)
                    }
                })
            })
        },
        // eslint-disable-next-line no-empty-pattern
        deleteChat({}, id) {
            return axios({
                method: 'delete',
                url: '/chats/' + id
            });
        },
        fetchChatMessages({commit, getters}, id) {
            const params = getters.GET_MESSAGES_PARAMS;

            if (params.page === 1) {
                commit('common/SET_LOADER_MINI', true, { root: true });
            }

            return axios({
                method: 'get',
                url: '/chats/' + id + '/chat-messages',
                params
            }).then((res) => {
                res.data.sort((a, b)  => new Date(a.sent_at) - new Date(b.sent_at));
                if (params.page > 1) {
                    const currentMessages = getters.GET_MESSAGES;

                    currentMessages.data = [...res.data, ...currentMessages.data];
                    currentMessages.meta = res.meta;
                    currentMessages.links = res.links;

                    commit('SET_MESSAGES', currentMessages);
                } else {
                    commit('SET_MESSAGES', res);
                }
            }).finally(() => {
                commit('common/SET_LOADER_MINI', false, { root: true });
            })
        },
        // eslint-disable-next-line no-empty-pattern
        deleteMessage({}, id) {
            return axios({
                method: 'delete',
                url: '/chat-messages/' + id
            });
        },
        // eslint-disable-next-line no-empty-pattern
        sendMessage({}, { chatId, data }) {
            return axios({
                method: 'post',
                url: '/chats/' + chatId + '/chat-messages/',
                data
            })
        },
        checkMessages({ commit }) {
            axios({
                method: 'get',
                url: '/user/check-new-messages'
            })
                .then((data) => {
                    commit('SET_HAS_NEW_MESSAGES', data.has_unread_message);
                    commit('common/SET_ALL_COUNT_UNREAD_NOTICES', data.all_count_unread_notices, { root: true });
                })
        },
        // eslint-disable-next-line no-unused-vars
        setRead({ commit, getters, dispatch }, chatId) {
            axios({
                method: 'post',
                url: '/chats/' + chatId + '/chat-messages/set-read'
            })
                .then((res) => {
                    let currentChats = getters.GET_CHATS;

                    if (currentChats) {
                        currentChats.data.forEach((item) => {
                            if (item.id === res.data.id) {
                                item.chat = res.data;
                            }
                        });
                        commit('SET_CHATS', currentChats);
                    }

                    dispatch('checkMessages');
                })
        }
    }
}
