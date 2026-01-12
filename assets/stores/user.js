import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
    state: () => ({
        type: null,
        email: null,
    }),
    actions: {
        login(type, userData) {
        this.type = type
        this.email = userData.email
        }
    }
})