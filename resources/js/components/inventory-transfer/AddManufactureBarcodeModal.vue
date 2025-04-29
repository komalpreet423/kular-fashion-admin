<template>
    <div class="modal fade" id="addManufactureBarcodeModal" tabindex="-1" aria-labelledby="addManufactureBarcodeModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addManufactureBarcodeModalLabel">Add manufacture code</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
  
          <div class="modal-body">
            <div class="search-box mb-2">
              <div class="position-relative">
                <input
                  type="number"
                  v-model="query"
                  class="form-control"
                  placeholder="Scan barcode"
                  @keydown.enter="addManufactureBarcode"
                  ref="barcodeInput"
                />
                <i class="bx bx-barcode search-icon"></i>
              </div>
            </div>
  
            <!-- Enter button -->
            <div class="d-grid gap-2">
              <button
                class="btn btn-primary"
                type="button"
                @click="addManufactureBarcode"
                :disabled="loading || !query"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span v-else>Enter</span>
              </button>
            </div>
  
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    props: {
      item: {
        type: Object,
        required: true
      }
    },
    data() {
      return {
        query: '',
        loading: false
      };
    },
    methods: {
      async addManufactureBarcode() {
        // if (String(this.query).length === 12 || String(this.query).length === 13) {
          try {
            this.loading = true;
  
            const response = await axios.post('/api/products/add-manufacture-barcode', {
              id: this.item.id,
              barcode: String(this.query),
            });
  
            const { success } = response.data;
            if (success) {
              // Hide Modal
              const addManufactureBarcodeModal = document.getElementById('addManufactureBarcodeModal');
              addManufactureBarcodeModal.classList.remove('show');
              addManufactureBarcodeModal.style.display = 'none';
  
              // Remove Backdrop
              const backdrop = document.querySelector('.modal-backdrop');
              if (backdrop) {
                backdrop.remove();
              }
  
              // Emit event
              this.$emit('item-scanned', this.query);
  
              // Reset query
              this.query = '';
            }
          } catch (error) {
            console.error(error);
          } finally {
            this.loading = false;
          }
        // }
      },
      focusOnInput() {
        this.$nextTick(() => {
          if (this.$refs.barcodeInput) {
            setTimeout(() => {
              this.$refs.barcodeInput.focus();
            }, 500);
          }
        });
      }
    },
    watch: {
      item: {
        handler() {
          this.focusOnInput();
        },
        immediate: true
      }
    }
  };
  </script>
  