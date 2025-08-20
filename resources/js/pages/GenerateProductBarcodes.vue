<template>
    <div class="d-flex justify-content-between">
        <h5>{{ selectedArticles.length }} selected Articles</h5>

        <button class="btn btn-primary btn-sm" @click="setBarcodesToBePrint" v-if="selectedArticles.length > 0">
            <i class="bx bx-printer"></i>
            Print
        </button>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ProductsForBarcode @toggle-selected-product="toggleSelection" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ProductsForBarcode from '../components/product/ProductsForBarcode.vue';

export default {
    components: {
        ProductsForBarcode
    },
    props: {
        links: {
            required: true
        },
        defaultProductsToBePrinted: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            selectedArticles: []
        };
    },
    watch: {
        selectedArticles: {
            handler() {
                $('#product-table').attr('data-selected-articles', this.selectedArticles);
            },
            deep: true
        }
    },
    mounted() {
        this.selectedArticles = [];
        $('table').on('change', '#select-all', (event) => {
            const checkboxes = $('#product-table .select-row');
            if (event.target.checked) {
                this.selectedArticles = checkboxes.map((_, checkbox) => $(checkbox).val()).get();
            } else {
                this.selectedArticles = [];
            }
        });

      /*  $('#product-table').on('click', 'tbody tr', (event) => {
            if (event.target.tagName !== 'TD') return;
            const row = $(event.currentTarget);
            const productId = row.find('.select-row').val();

            const index = this.selectedArticles.indexOf(productId);
            if (index === -1) {
                this.selectedArticles.push(productId);
            }
        });*/

        $('#product-table').on('change', '.select-row', (event) => {
            this.toggleSelection($(event.target).val());
            const allChecked = $('#product-table .select-row').length === $('#product-table .select-row:checked').length;
            $('#select-all').prop('checked', allChecked);
        });
    },
    methods: {
        toggleSelection(productId) {
            const index = this.selectedArticles.indexOf(productId);
            if (index === -1) {
                this.selectedArticles.push(productId);
            } else {
                this.selectedArticles.splice(index, 1);
            }
        },
        setBarcodesToBePrint() {
            const selectedProductIds = this.selectedArticles;
            const barcodesToBePrinted = [];

            selectedProductIds.forEach((productId) => {
                const productContainer = $(`[data-product-barcode-quantity="${productId}"]`);

                const quantitiesToBePrint = $(productContainer).find('.barcode-quantity').not('[disabled]').filter(function () {
                    return parseFloat($(this).val()) > 0;
                });

                const product = [];
                quantitiesToBePrint.each(function () {
                    const inputId = $(this).attr('id');
                    const inputValue = $(this).val();
                    const printQty = $(this).data('double') ? parseInt(inputValue) * 2 : inputValue;

                    product.push({ id: inputId, orignalQty: inputValue, printQty: printQty });
                });

                barcodesToBePrinted.push({
                    productId,
                    product,
                });
            });

            $.ajax({
                url: '/printbarcode-store-session',
                type: 'POST',
                data: {
                    _token: $('[name="csrf-token"]').attr('content'),
                    barcodesToBePrinted
                },
                success: function (resp) {
                    if (resp.success) {
                        window.location.href = '/products/print-barcodes/preview';
                    }
                }, error: function (err) {
                    console.log(err);
                }
            });
        }
    }
};
</script>