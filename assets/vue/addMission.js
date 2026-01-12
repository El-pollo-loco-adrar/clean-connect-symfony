import { createApp } from 'vue'
import MultipleSelectCheckbox from '../components/MultipleSelectCheckbox.vue' // chemin relatif depuis addMissions.js

createApp({
    data() {
        return {
            selectedSkills: [],
            loading: false,
        }
    },
    components: {
        MultipleSelectCheckbox
    },
    mounted() {
        console.log('Vue AddMission chargé 🚀')
    }
}).mount('#add-mission-app')