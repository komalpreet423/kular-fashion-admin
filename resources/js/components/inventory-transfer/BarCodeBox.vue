<template>
    <div class="search-box mb-2">
        <div class="position-relative">
            <input type="number" v-model="query" class="form-control" placeholder="Enter barcode" autofocus
                @input="addToCart" ref="barcodeInput">
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
            if (this.query.toString().length === 13) {
                const barcode = this.query;
                this.query = '';
                const response = await axios.get(`/product-validate/${barcode}?from=${this.fromStore}`);
                const { product } = response.data;
                if (product) {
                    this.$emit('transfer-item', product);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Product Barcode is invalid.',
                        icon: 'error',
                        confirmButtonText: 'Okay'
                    });
                }
            }
        },
        focusOnInput(){
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
            immediate: true,
        }
    }
};
</script>