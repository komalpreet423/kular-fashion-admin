<template>
    <div class="row bg-dark-subtle mt-2">
        <div class="col-md-6 my-1" v-for="(addedCondition, index) in conditions" :key="index">
            <div class="row">
                <div :class="addedCondition.subFields?.length > 0 ? 'col-md-5' : 'col-md-10'">
                    <div class="form-group">
                        <label class="mb-0" :for="`${conditionType}_${addedCondition.name}`">{{ addedCondition.label
                            }}</label>

                        <select class="form-control" :class="{ multiSelect: addedCondition.multiple }"
                            :id="`${conditionType}_${addedCondition.name}`" :multiple="addedCondition.multiple"
                            :name="`${conditionType}[${addedCondition.name}]${addedCondition.multiple ? '[]' : ''}`"
                            v-model="selectedValues[conditionType][addedCondition.name]"
                            v-if="addedCondition.type === 'select'">
                            <option disabled>Select {{ addedCondition.label }}</option>
                            <option v-for="(field, index) in addedCondition.values" :value="field.id">{{
                                field.value }} </option>
                        </select>

                        <div class="mb-2" v-else-if="addedCondition.type === 'range'">
                            <div class="price-input-container">
                                <div class="row justify-content-between">
                                    <div class="col-4 d-flex align-items-center gap-1">
                                        <label :for="`${conditionType}MinPrice`">Min: </label>
                                        <input type="number" :id="`${conditionType}MinPrice`"
                                            :name="`${conditionType}[${addedCondition.name}][min]`" class="form-control"
                                            v-model="minPrice">
                                    </div>
                                    <div class="col-4 d-flex align-items-center gap-1">
                                        <label :for="`${conditionType}MinPrice`">Max: </label>
                                        <input type="number" :id="`${conditionType}MinPrice`"
                                            :name="`${conditionType}[${addedCondition.name}][max]`" class="form-control"
                                            v-model="maxPrice">
                                    </div>
                                </div>
                                <div class="slider-container mt-2">
                                    <div class="price-slider" :style="sliderStyle"></div>
                                </div>
                            </div>

                            <!-- Slider -->
                            <div class="range-input">
                                <input type="range" class="min-range" v-model="minPrice" min="0" max="10000" value="0"
                                    step="1">
                                <input type="range" class="max-range" v-model="maxPrice" min="0" max="10000"
                                    value="5000" step="1">
                            </div>
                        </div>

                        <input type="text" v-else-if="addedCondition.type === 'date'">

                        <input v-else :id="addedCondition.name" :name="`${conditionType}[${addedCondition.name}]`"
                            :type="addedCondition.type || 'text'" class="form-control"
                            :placeholder="`Enter ${addedCondition.label}`"
                            v-model="selectedValues[conditionType][addedCondition.name]">
                    </div>
                </div>

                <div v-if="addedCondition.subFields?.length > 0"
                    :class="addedCondition.subFields?.length > 0 ? 'col-md-5' : 'col-md-10'">
                    <div v-for="(subField, index) in addedCondition.subFields" :key="index">
                        <div
                            v-if="subField.basedOn && subField.basedOn === selectedValues[conditionType][addedCondition.name]">
                            <label class="mb-0" :for="subField.number_of_days">{{ subField.label }}</label>

                            <input :name="`${conditionType}[${subField.name}]`" v-on="changeSubField(subField)"
                                :type="subField.type === 'date' ? 'text' : subField.type" :value="subField.value"
                                class="form-control" :class="{ 'date-range-picker': subField.type === 'date' }"
                                :placeholder="`Enter ${subField.label}`">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="mt-4 btn btn-danger" @click="removeCondition(index)"><i
                            class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        conditionType: {
            type: String,
            default: 'include'
        },
        conditions: {
            type: Array,
            default: []
        }
    },
    emits: ['removeCondition'],
    data() {
        return {
            selectedValues: {
                include: {
                    published_within: 'Days',
                    product_types: [],
                    price_status: 'Reduce Item Only',
                },
                exclude: {
                    published_within: 'Days',
                    product_types: [],
                    price_status: 'Reduce Item Only',
                }
            },
            minPrice: 0,
            maxPrice: 5000,
        }
    },
    computed: {
        sliderStyle() {
            const minPercentage = (this.minPrice / 10000) * 100;
            const maxPercentage = (this.maxPrice / 10000) * 100;

            const left = Math.min(minPercentage, maxPercentage);
            const right = 100 - Math.max(minPercentage, maxPercentage);

            return {
                left: `${left}%`,
                right: `${right}%`
            };
        }
    },
    watch: {
        conditions: {
            handler(newConditions) {
                newConditions.forEach(condition => {
                    let defaultValue = '';
                    this.selectedValues[this.conditionType][condition.name] = condition.defaulValue || defaultValue;

                    if (condition.name === 'price_range') {
                        this.minPrice = condition.defaulValue?.min || 0;
                        this.maxPrice = condition.defaulValue?.max || 10000;
                    }
                });
            },
            immediate: true,
            deep: true
        },
        minPrice() {
            this.updateSlider();
        },
        maxPrice() {
            this.updateSlider();
        }
    },
    methods: {
        removeCondition(conditionIndex) {
            this.$emit('removeCondition', {
                conditionIndex: conditionIndex,
                conditionType: this.conditionType
            });
        },
        changeSubField(subField) {
            setTimeout(function () {
                flatpickr('.date-range-picker', {
                    mode: "range",  // Enable date range selection
                    dateFormat: "d-m-y",  // Format dates as YYYY-MM-DD (adjust as needed)
                    locale: {
                        firstDayOfWeek: 1,  // Start the week on Monday
                    },
                });
            }, 10)

        },
        updateSlider() {
            if (this.minPrice < 0) {
                this.minPrice = 0;
            }
            if (this.maxPrice > 10000) {
                this.maxPrice = 10000;
            }
        }
    }
}
</script>


<style scoped>
/* Remove Arrows/Spinners */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.slider-container {
    width: 100%;
}

.slider-container {
    height: 6px;
    position: relative;
    background: #e4e4e4;
    border-radius: 5px;
}

.slider-container .price-slider {
    height: 100%;
    left: 0%;
    right: 50%;
    position: absolute;
    border-radius: 5px;
    background: #556ee6;
}

.range-input {
    position: relative;
}

.range-input input {
    position: absolute;
    width: 100%;
    height: 5px;
    background: none;
    top: -5px;
    pointer-events: none;
    cursor: pointer;
    -webkit-appearance: none;
}

/* Styles for the range thumb in WebKit browsers */
input[type="range"]::-webkit-slider-thumb {
    height: 18px;
    width: 18px;
    border-radius: 70%;
    background: #555;
    pointer-events: auto;
    -webkit-appearance: none;
}
</style>