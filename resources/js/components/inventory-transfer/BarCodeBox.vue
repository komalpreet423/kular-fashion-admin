<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input
                type="text"
                v-model="query"
                class="form-control"
                placeholder="Enter barcode"
                autofocus
                @input="handleInput"
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
            default: () => ({})
        },
        fromStore: {
            type: Number,
            required: true
        }
    },
    methods: {
        async addToCart(barcode) {
            if (this.loading) return;

            this.loading = true;
            try {
                const response = await axios.get(`/product-validate/${barcode}?from=${this.fromStore}`);
                const { product } = response.data;

                if (product) {
                    this.$emit('transfer-item', product);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product barcode is invalid.',
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
                this.query = '';
                this.loading = false;
            }
        },

        handleInput(event) {
            let value = event.target.value.replace(/\D/g, ''); // keep digits only

            if (value.length > 13) {
                value = value.slice(0, 13); // trim to 13 digits
            }

            this.query = value;

            if (value.length === 13) {
                this.addToCart(value);
            }
        },

        handlePaste(event) {
            event.preventDefault();
            const pastedValue = event.clipboardData.getData('text');
            const digits = pastedValue.replace(/\D/g, '').slice(0, 13); // keep only first 13 digits
            this.query = digits;

            if (digits.length === 13) {
                this.$nextTick(() => {
                    this.addToCart(digits);
                });
            }
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
