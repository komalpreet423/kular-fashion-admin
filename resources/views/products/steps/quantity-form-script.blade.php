<div class="modal fade" id="addVariantModal" tabindex="-1" aria-labelledby="addVariantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVariantModalLabel">Add New Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVariantForm">
                    <div class="mb-3">
                        <label for="supplier_color_code" class="form-label">Supplier Color Code</label>
                        <input type="text" id="supplier_color_code" class="form-control"
                            placeholder="Enter Supplier Color Code" required>
                        <div id="supplierColorCodeError" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_color_name" class="form-label">Supplier Color Name</label>
                        <input type="text" id="supplier_color_name" class="form-control"
                            placeholder="Enter Supplier Color Name" required>
                        <div id="supplierColorNameError" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="color_select" class="form-label">Select Color</label>
                        <select id="color_select" class="form-control" required>
                            <option value="" disabled selected>Select Color</option>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->name }} ({{ $color->code }})
                                </option>
                            @endforeach
                        </select>
                        <div id="colorSelectError" class="invalid-feedback"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveVariantBtn" class="btn btn-primary">Save Variant</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            $('#color_select').select2({
                width: '100%',
                dropdownParent: $('#addVariantModal')
            });

            $(document).on('click', '.color-selector', function() {
                let isSelected = false;
                if ($(this).hasClass('selected')) {
                    isSelected = true;
                }

                $('.color-selector').removeClass('selected');

                if (!isSelected) {
                    $(this).addClass('selected');
                    $('.copy-quantity-btn').removeClass('d-none');
                    $(this).parents('tr').find('.copy-quantity-btn').addClass('d-none');
                } else {
                    $('.copy-quantity-btn').addClass('d-none');
                }
            });

            $('#add-variant-btn').on('click', function() {
                $('#addVariantModal').modal('show');
            });

            $(document).on('focus', 'input[type="number"]', function() {
                $(this).select();
            });

            $(document).on('click', '.copy-quantity-btn', function() {
                const $selectedColor = $('.color-selector.selected').first();

                if ($selectedColor.length > 0) {
                    const id = $selectedColor.closest('[data-id]').data('id');

                    if (id) {
                        const colorIdToBeCopied = id.split('-')[1];
                        const colorIdForCopy = $(this).data('color-id');

                        // Loop over all size elements only once
                        $('[data-size-id]').each(function() {
                            const $this = $(this);
                            const sizeId = $this.data('size-id');
                            const quantityToBeCopied = $(
                                `[name="quantity[${colorIdToBeCopied}][${sizeId}]"]`).val();

                            // Set the quantity for the other color's size
                            $(`[name="quantity[${colorIdForCopy}][${sizeId}]"]`).val(
                                quantityToBeCopied);
                        });
                    }
                }
            });
        })

        $(function() {
            $('#saveVariantBtn').on('click', function() {
                $('#supplier_color_code').removeClass('is-invalid');
                $('#supplier_color_name').removeClass('is-invalid');
                $('#color_select').removeClass('is-invalid');

                let formData = {
                    supplier_color_code: $('#supplier_color_code').val(),
                    supplier_color_name: $('#supplier_color_name').val(),
                    color_select: $('#color_select').val(),
                    product_id: '{{ $product->id ?? 0 }}',
                    _token: '{{ csrf_token() }}'
                };

                $.post('/add-variant', formData).done(function(response) {
                        if (response.success) {
                            $('#addVariantModal').modal('hide');
                            $('#addVariantForm')[0].reset();
                            $('.actionColumn').removeClass('d-none');

                            let $tbody = $('table tbody');
                            let $sizeHeader = $('#sizeHeader');

                            let sizes = $sizeHeader.find('th')
                                .slice(1, -1) // Skip the first "Size" header and remove the last header
                                .map(function() {
                                    return $(this).attr('data-size-id');
                                })
                                .get();

                            $(`#color_select [value="${response.data.color_id}"]`).remove();
                            $('#color_select').trigger('chosen:updated');

                            let uniqueId = $('.quantities-table [data-id]').length + 1;
                            let $newRow = $(`<tr data-id="rm-${response.data.color_id}" data-color-detail='${JSON.stringify(response.data)}'></tr>`);

                            let $newTh = $(
                                '<th class="d-flex align-items-center justify-content-center flex-column text-center change-color-image-modal"></th>'
                            ).html(
                                `<div class="me-1 d-color-code color-selector" style="background: ${response.data.ui_color_code}"></div>
                                    <label class="font-size-12 fw-bold">${response.data.color_name}(${response.data.color_code})</label>`
                            );
                            $newRow.append($newTh);

                            $.each(sizes, function(index, size) {
                                let $newTd = $('<td></td>');
                                let quantityCell =
                                    `<input type="number" name="quantity[${response.data.color_id}][${size}]" value="0" min="0" class="form-control">`;
                                @isset($product)
                                    quantityCell +=
                                        `<h6 class="mt-1 mb-0">Total in: <b>0</b></h6>`;
                                @endisset
                                $newTd.html(quantityCell);
                                $newRow.append($newTd);
                            });

                            @isset($product)
                                $newRow.append('<td class="fs-5 text-center">0</td>');
                            @endisset


                            let copyButtonAdditionalClass = ``;
                            if (!$('.color-selector').hasClass('selected')) {
                                copyButtonAdditionalClass = 'd-none';
                            }

                            let $actionColumn = $('<td class="actionColumn"></td>');
                            $actionColumn.html(`
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.remove-variant', '') }}/${response.data.color_id}" class="btn btn-danger btn-sm"> 
                                <i class="fas fa-trash-alt"></i>
                                </a>
                                <button type="button" class="btn btn-secondary copy-quantity-btn btn-sm ${copyButtonAdditionalClass}" data-color-id="${response.data.color_id}">
                                    <i class="mdi mdi-content-copy fs-6"></i>
                                </button>
                            </div>`);

                            $('#copy_quantity_for_color').append(
                                `<option value="${response.data.color_id}">${response.data.color_name} (${response.data.color_code})</option>`
                            );

                            $newRow.append($actionColumn);
                            $tbody.prepend($newRow);
                        }
                    })
                    .fail(function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.supplier_color_code) {
                            $('#supplier_color_code').addClass('is-invalid');
                            $('#supplierColorCodeError').text(errors.supplier_color_code[0]);
                        }
                        if (errors.supplier_color_name) {
                            $('#supplier_color_name').addClass('is-invalid');
                            $('#supplierColorNameError').text(errors.supplier_color_name[0]);
                        }
                        if (errors.color_select) {
                            $('#color_select').addClass('is-invalid');
                            $('#colorSelectError').text(errors.color_select[0]);
                        }
                    });
            });
        });
    </script>
@endpush
