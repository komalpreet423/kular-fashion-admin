<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input
                type="text"
                :value="query"
                class="form-control"
                placeholder="Enter barcode"
                autofocus
                @input="sanitizeInput"
                @keyup="addToCart"
                @paste="handlePaste"
                ref="barcodeInput"
            />
            <i class="bx bx-barcode search-icon"></i>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            query: '',
            loading: false
        };
    },
    props: {
        itemToBeAdd: {
            type: Object,
            default: {}
        },
        fromStore: {
            type: Number,
            required: true
        }
    },
    methods: {
        async addToCart() {
            const barcode = this.query.toString().trim();
            if (barcode.length === 13) {
                this.query = '';
                this.loading = true;
                try {
                    const response = await axios.get(`/product-validate/${barcode}?from=${this.fromStore}`);
                    const { product } = response.data;

                    if (product) {
                        // Directly emit product to parent component
                        this.$emit('transfer-item', product);
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Product Barcode is invalid.',
                            icon: 'error',
                            confirmButtonText: 'Okay'
                        });
                    }
                } catch (error) {
                    console.error('Error fetching product:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to validate barcode.',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                } finally {
                    this.loading = false;
                }
            }
        },

        handlePaste(event) {
            event.preventDefault();
            const pastedValue = event.clipboardData.getData('text');
            const cleaned = pastedValue.replace(/\D/g, '');
            this.query = cleaned;
        },

        focusOnInput() {
            this.$nextTick(() => {
                if (this.$refs.barcodeInput) {
                    this.$refs.barcodeInput.focus();
                }
            });
        }
    },

    watch: {
        itemToBeAdd: {
            handler() {
                this.focusOnInput();
            },
            immediate: true
        }
    }
};
</script>
