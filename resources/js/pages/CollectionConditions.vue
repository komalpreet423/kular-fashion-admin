<template>
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Basic Information</h4>
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        <label for="collection_name">Collection Name<span class="text-danger">*</span></label>
                        <input class="form-control" v-model="collection.name" name="collection_name"
                            placeholder="Enter Collection Name" v-bind:class="{ 'is-invalid': errors.name }" />

                        <span v-if="errors.name" class="invalid-feedback">{{ errors.name }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label for="collection-status" class="form-label">Status</label>

                    <select name="status" id="collection-status" class="form-control" v-model="savedCollection.status">
                        <option value="1" v-bind:value="1">Active</option>
                        <option value="0" v-bind:value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-sm-6 col-md-3">
                    <label for="status">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" @change="previewImage">

                    <div class="row d-block" v-if="imagePreviewUrl">
                        <div class="col-md-8 mt-2">
                            <img :src="imagePreviewUrl" id="preview-collection" class="img-fluid w-50" alt="Preview">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Conditions</h4>
            <div class="col-md-12 mb-3">
                <div class="row">
                    <div class="col-md-6 d-flex justify-content-between">
                        <h6>Include Conditions</h6>
                        <button type="button" class="btn btn-sm btn-secondary" @click="addNewCondition('include')">Add
                            new
                            condition</button>
                    </div>
                </div>

                <AddedConditions :conditionType="'include'" :conditions="conditions.include"
                    @removeCondition="removeCondition"></AddedConditions>
            </div>

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 d-flex justify-content-between">
                        <h6>Exclude Conditions</h6>
                        <button type="button" class="btn btn-sm btn-secondary" @click="addNewCondition('exclude')">Add
                            new
                            condition</button>
                    </div>
                </div>
                <AddedConditions :conditionType="'exclude'" :conditions="conditions.exclude"
                    @removeCondition="removeCondition"></AddedConditions>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Listing Page Content</h4>
            <div>
                <h4 class="card-title">Summary</h4>
                <textarea name="summary" id="summary" class="editor" rows="2">{{ savedCollection.summary }}</textarea>
            </div>
            <div class="mt-3">
                <h4 class="card-title">Description</h4>
                <textarea name="description" id="description" class="editor"
                    rows="2">{{ savedCollection.description }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">SEO</h4>
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-sm-10">
                            <label for="heading">Heading</label>
                            <input name="heading" id="heading" class="form-control" :value="savedCollection.heading"
                                placeholder="Meta title" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="mb-3">
                        <label for="meta_title">Meta title</label>
                        <input name="meta_title" id="meta_title" class="form-control"
                            :value="savedCollection.meta_title" placeholder="Meta title" />
                    </div>
                    <div class="mb-3">
                        <label for="meta_keywords">Meta Keywords</label>
                        <input name="meta_keywords" id="meta_keywords" class="form-control"
                            :value="savedCollection.meta_keywords" placeholder="Meta Keywords" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <label for="meta_description">Meta Description</label>
                        <textarea name="meta_description" class="form-control" id="meta_description" rows="5"
                            placeholder="Meta Description">{{ savedCollection.meta_description }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-6 mb-2">
            <button type="submit" class="btn btn-primary w-md" :disabled="!collection.name">Submit</button>
        </div>
    </div>

    <AddConditionModal :conditionType="conditionType" :addedConditions="conditions[conditionType]"
        @addCondition="addCondition" :conditionMap="conditionMap"></AddConditionModal>
</template>

<script>
import axios from 'axios';
import AddConditionModal from '../components/collections/AddConditionModal.vue';
import AddedConditions from '../components/collections/AddedConditions.vue';

let cancelTokenSource = null;

export default {
    components: {
        AddConditionModal,
        AddedConditions
    },
    props: {
        conditionDependencies: {
            type: Object,
            requied: true
        },
        savedCollection: {
            type: Object,
            default: {}
        }
    },
    data() {
        return {
            conditionType: 'include',
            conditions: {
                'include': [],
                'exclude': []
            },
            collection: {
                name: this.savedCollection.name || ''
            },
            errors: {
                name: ''
            },
            imagePreviewUrl: this.savedCollection.image
                ? `${window.location.origin}/${this.savedCollection.image}`
                : '',

            conditionMap: {
                tags: "Have one of these tags",
                product_types: "Any of these product types",
                price_list: "Be in the price list",
                price_range: "Be in the price range",
                price_status: "Have the price status",
                published_within: "Have been published within"
            }
        };
    },
    mounted() {
        if (this.savedCollection) {
            let includeConditions = this.savedCollection.include_conditions;
            if (includeConditions) {
                includeConditions = JSON.parse(includeConditions);

                for (let [key, value] of Object.entries(includeConditions || {})) {
                    const conditionLabel = this.conditionMap[key];

                    if (conditionLabel) {
                        const conditionObject = {
                            name: key,
                            label: conditionLabel,
                            defaulValue: value
                        };

                        this.addCondition(conditionObject);
                    }
                }
            }

            let excludeConditions = this.savedCollection.exclude_conditions;
            if (excludeConditions) {
                excludeConditions = JSON.parse(excludeConditions);

                for (let [key, value] of Object.entries(excludeConditions || {})) {
                    const conditionLabel = this.conditionMap[key];
                    const conditionObject = {
                        name: key,
                        label: conditionLabel,
                        defaulValue: value
                    };

                    this.addCondition(conditionObject, 'exclude');
                }
            }
        }

        $('[name="image"]').change(function (event) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview-collection').attr('src', e.target.result).removeAttr('hidden');
            }
            reader.readAsDataURL(this.files[0]);
        });
    },
    watch: {
        'collection.name': function (name) {
            // If there's a pending request, cancel it
            if (cancelTokenSource) {
                cancelTokenSource.cancel('Request canceled due to changing the name');
            }

            // Create a new CancelToken for this request
            cancelTokenSource = axios.CancelToken.source();

            let paylad = {
                name,
                id: this.savedCollection.id || null
            }

            axios.post('/api/collections/check-name', paylad, {
                cancelToken: cancelTokenSource.token
            })
                .then((response) => {
                    this.errors.name = '';
                })
                .catch((error) => {
                    if (axios.isCancel(error)) {
                        console.log('Request canceled:', error.message);
                    } else if (error.response && error.response.status === 400) {
                        this.errors.name = error.response.data.message;
                    } else {
                        console.error('There was an error!', error);
                    }
                });
        }
    },
    methods: {
        addNewCondition(conditionType) {
            this.conditionType = conditionType;
            $('#addConditionModal').modal('show');
        },
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePreviewUrl = URL.createObjectURL(file);
            }
        },
        removeCondition(payload) {
            let selectedCondition = this.conditions[payload.conditionType][payload.conditionIndex];
            if (selectedCondition.type === 'select' && selectedCondition.multiple) {
                $(`#${payload.conditionType}_${selectedCondition.name}`).chosen('destroy');

                setTimeout(() => {
                    $('.multiSelect').each(function () {
                        let defaultPlaceholder = $(this).find('option').first().html();

                        $(this).chosen({
                            width: '100%',
                            placeholder_text_multiple: defaultPlaceholder,
                        });
                    });

                }, 100);
            }

            this.conditions[payload.conditionType].splice(payload.conditionIndex, 1);
        },
        addCondition(condition) {
            switch (condition.name) {
                case 'tags':
                    condition.type = 'select';
                    condition.multiple = true;
                    condition.values = this.conditionDependencies.tags;
                    break;
                case 'product_types':
                    condition.type = 'select';
                    condition.multiple = true;
                    condition.values = this.conditionDependencies.ProductTypes;
                    break;
                case 'price_list':
                    condition.type = 'number';
                    break;
                case 'price_range':
                    condition.type = 'range';
                    condition.values = { min: 0, max: this.conditionDependencies.maxProductPrice };
                    break;
                case 'published_within':
                    condition.type = 'select';

                    condition.values = [{
                        id: 'Days',
                        value: 'Days',
                    }, {
                        id: 'Range',
                        value: 'Range',
                    }];

                    condition.subFields = [{
                        type: 'number',
                        name: 'published_within_number_of_days',
                        label: 'Number Of Days',
                        value: 30,
                        basedOn: 'Days'
                    }, {
                        type: 'date',
                        name: 'published_between_dates',
                        label: 'Published Between',
                        multiple: true,
                        basedOn: 'Range'
                    }];

                    let savedData = this.savedCollection[`${this.conditionType}_conditions`];
                    if (savedData) {
                        savedData = JSON.parse(savedData);

                        let subfieldKey = 'published_between_dates';
                        if (savedData?.published_within_number_of_days) {
                            subfieldKey = 'published_within_number_of_days';
                        }

                        if (savedData) {
                            const specificSubField = condition.subFields.find(subField => subField.name === subfieldKey);
                            specificSubField.value = savedData[subfieldKey];
                        }
                    }

                    break;
                case 'price_status':
                    condition.type = 'select';
                    condition.values = [{
                        id: 'Reduce Item Only',
                        value: 'Reduce Item Only'
                    }, {
                        id: 'Full Price Item Only',
                        value: 'Full Price Item Only'
                    }];
                    break;
                default:
                    condition.type = 'text';
                    break;
            }

            this.conditions[this.conditionType].push(condition);

            setTimeout(function () {
                if (condition.type === 'select' && condition.multiple) {
                    $('.multiSelect').each(function () {
                        let defaultPlaceholder = $(this).find('option').first().html();

                        $(this).chosen({
                            width: '100%',
                            placeholder_text_multiple: defaultPlaceholder,
                        });
                    });
                }
            }, 10);
        }
    }
};
</script>