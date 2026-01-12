<template>
    <div class="multi-select-checkbox">
        <button type="button" @click="toggleDropdown" class="dropdown-btn">
        {{ selectedLabels.length ? selectedLabels.join(', ') : placeholder }}
        </button>
        <div v-show="open" class="dropdown-list">
        <label v-for="option in options" :key="option.id" class="dropdown-item">
            <input
            type="checkbox"
            :value="option.id"
            v-model="selected"
            />
            {{ option.label }}
        </label>
        </div>
    </div>
</template>

<script>
    export default {
    props: {
        options: {
        type: Array,
        required: true
        },
        value: {
        type: Array,
        default: () => []
        },
        placeholder: {
        type: String,
        default: 'Sélectionnez...'
        }
    },
    data() {
        return {
        open: false,
        selected: this.value
        };
    },
    computed: {
        selectedLabels() {
        return this.options
            .filter(opt => this.selected.includes(opt.id))
            .map(opt => opt.label);
        }
    },
    watch: {
        selected(newVal) {
        this.$emit('input', newVal);
        },
        value(newVal) {
        this.selected = newVal;
        }
    },
    methods: {
        toggleDropdown() {
        this.open = !this.open;
        }
    }
    };
    </script>

<style scoped> 
    .multi-select-checkbox {
    position: relative;
    display: inline-block;
    width: 250px;
    }

    .dropdown-btn {
    width: 100%;
    text-align: left;
    padding: 5px 10px;
    }

    .dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 10;
    background: white;
    border: 1px solid #ccc;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    }

    .dropdown-item {
    display: flex;
    align-items: center;
    padding: 2px 10px;
    }
</style> 