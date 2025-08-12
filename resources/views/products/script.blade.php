<script type="text/javascript">
    $(document).ready(function() {
        $('#brandFilter, #typeFilter, #departmentFilter').select2({
            width: '100%',
        });

        var selectedProducts = [];
        var unselectedProducts = [];

        var table = $('#product-table').DataTable({
            processing: true,
            lengthMenu: [ 
                [10, 25, 50, 100, 250, 350, 500, -1], 
                [10, 25, 50, 100, 250, 350, 500] 
            ],
            serverSide: true,
            dom: 'rt<"d-flex justify-content-between align-items-center "<"dt-info-left"i><"d-flex align-items-center gap-2"l p>><"clear">',
            
            ajax: {
                url: "{{ route('get.products') }}",
                data: function(d) {
                    d.page = Math.floor(d.start / d.length) + 1;
                    d.brand_id = $('#brandFilter').val();
                    d.product_type_id = $('#typeFilter').val();
                    d.department_id = $('#departmentFilter').val();
                    d.order = d.order; // Pass sorting parameters
                    d.related_type = "{{ request()->route('type') }}";
                    d.related_type_id = "{{ request()->route('id') }}";
                }
            },
            columns: [{
                    title: '<input type="checkbox" class="form-check-input" id="select-all-checkbox">',
                    data: null,
                    render: function(data, type, row) {
                        let selectedProducts = $('[data-selected-products]').attr('data-selected-products');
                        selectedProducts = selectedProducts.split(',');

                        let checked = selectedProducts.includes(String(row.id)) ? 'checked' : '';

                        if (selectedProducts.includes('-1')) {
                            checked = 'checked';
                            selectedProducts.push(row.id);
                        }

                        if (unselectedProducts.includes(String(row.id))) {
                            checked = '';
                        }

                        return `<input type="checkbox" class="product-checkbox form-check-input" value="${row.id}" ${checked}>`;
                    },
                    orderable: false
                },
                {
                    title: "Article Code",
                    data: 'article_code'
                },
                {
                    title: "Brand",
                    data: 'brand.name'
                },
                {
                    title: "Product Type",
                    data: 'product_type.name'
                },
                {
                    title: "Department",
                    data: 'department.name'
                },
                {
                    title: "Short Description",
                    data: 'short_description'
                },
                {
                    title: "Manufacture Code",
                    data: 'manufacture_code'
                },
                {
                    title: "Price",
                    data: 'price'
                },
                {
                    title: "Visible",
                    data: null, // Don't expect data from server
                    render: function (data, type, row) {
                        if (type === 'display') {
                            if (row.web_info && row.web_info.status == 1) {
                                return '<i class="fa fa-circle" aria-hidden="true" style="color:green;"></i>';
                            } else {
                                return '<i class="fa fa-circle" aria-hidden="true" style="color:red;"></i>';
                            }
                        }
                        return '';
                    }
                },
                {
                    title: "Actions",
                    data: null,
                    render: function(data, type, row) {
                        var actions = '<div class="action-buttons">';
                        @can('view products')
                        actions += `<a href="{{ route('products.show', ':id') }}" class="btn btn-secondary btn-sm py-0 px-1">`
                            .replace(/:id/, row.id);
                        actions += `<i class="fas fa-eye"></i>`;
                        actions += `</a>`;
                        @endcan

                        @can('edit products')
                        actions += `<a href="{{ route('products.edit.web-configuration', ':id') }}" class="btn btn-success btn-sm edit py-0 px-1">`
                            .replace(/:id/, row.id);
                        actions += `<i class="fas fa-image"></i>`;
                        actions += `</a>`;

                        actions += `<a href="{{ route('products.edit', ':id') }}" class="btn btn-primary btn-sm edit py-0 px-1">`
                            .replace(/:id/, row.id);
                        actions += `<i class="fas fa-pencil-alt"></i>`;
                        actions += `</a>`;
                        @endcan

                        @can('delete products')
                        actions += `<button data-source="product" data-endpoint="{{ route('products.destroy', ':id') }}" class="delete-btn btn btn-danger btn-sm py-0 px-1">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>`
                            .replace(/:id/, row.id);
                        @endcan

                        return actions;
                    }
                }
            ],
            order: [
                [3, 'asc']
            ], // Default sorting on Manufacture Code (3rd column)
            drawCallback: function(settings) {
                let api = this.api();
                $('#product-table th, #product-table td').addClass('p-1');

                let rows = api.rows({
                    page: 'current'
                }).data().length;

                let searchValue = $('#custom-search-input').val();
                if (rows === 0 && searchValue) {
                    $('#add-product-link').attr('href', `{{ route('products.create') }}?mfg_code=${searchValue}`)
                } else {
                    $('#add-product-link').attr('href', `{{ route('products.create') }}`)
                }
                updateSelectedCount();
            }
        });

        function updateSelectedCount() {
            // let selectedCount = $('.product-checkbox:checked').length;

            // // Remove existing count display
            // $('#selected-count-display').remove();

            // // Show only if at least one checkbox is selected
            // if (selectedCount > 0) {
            //     let paginateContainer = $('.dataTables_paginate');
            //     if (paginateContainer.length) {
            //         $('#product-table_previous').before('<li style="display:flex; align-items:center;"><span id="selected-count-display" class="me-3 text-primary fw-bold">Selected: ' + selectedCount + '</span></li>');
            //     }
            // }
        }
        $(function () {
            const $assign = $('#category');
            const $unassign = $('#un-category');
            $assign.select2({
                placeholder: "Assign Category",
                allowClear: true,
                width: '100%'
            });
            $unassign.select2({
                placeholder: "Un-Assign Category",
                allowClear: true,
                width: '100%'
            });
            function syncOptions(source, target) {
                const selectedValues = $(source).val() || [];
                $(target).find('option').prop('disabled', false);
                selectedValues.forEach(function (val) {
                    $(target).find('option[value="' + val + '"]').prop('disabled', true);
                });
                $(target).select2();
            }
            $assign.on('change', function () {
                syncOptions($assign, $unassign);
            });
            $unassign.on('change', function () {
                syncOptions($unassign, $assign);
            });
        });

        $(document).on('change', '.product-checkbox', function() {
            updateSelectedCount();
        });


        $(document).on('change', '#select-all-checkbox', function() {
            $('.product-checkbox').prop('checked', this.checked).trigger('change');
        });

        $('#brandFilter, #typeFilter, #departmentFilter').on('change', function() {
            table.ajax.reload();
        });

        $('#product-table_filter').prepend(
            `<input type="text" id="custom-search-input" class="form-control" placeholder="Search Products">`
        );

        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        function updateSelectedProducts() {
            $('#product-table').attr('data-selected-products', selectedProducts.join(','));
            $('#product-table').attr('data-unselected-products', unselectedProducts.join(','));

            if (!$('.product-checkbox:checked').length) {
                $('#bulk-edit-button').addClass('d-none');
                $('#action-section').addClass('d-none');
            } else {
                $('#bulk-edit-button').removeClass('d-none');
                $('#action-section').removeClass('d-none');
            }
        }

        // Select all checkboxes
        $('#select-all-checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                selectedProducts = ['-1'];
            } else {
                selectedProducts = [];
            }

            var checked = this.checked;
            $('.product-checkbox').each(function() {
                if (!unselectedProducts.includes($(this).val())) {
                    this.checked = checked;
                }
            });

            updateSelectedProducts();
        });

        // Individual checkbox selection
        $('#product-table').on('change', '.product-checkbox', function() {
            if ($(this).is(':checked')) {
                selectedProducts.push($(this).val());
            } else {
                let selectedProductIndex = selectedProducts.indexOf($(this).val());
                if (selectedProductIndex !== -1) {
                    selectedProducts.splice(selectedProductIndex, 1);
                }
            }

            if (!$(this).is(':checked') && $('#select-all-checkbox:checked').length) {
                unselectedProducts.push($(this).val());
            } else {
                let unselectedProductIndex = unselectedProducts.indexOf($(this).val());
                if (unselectedProductIndex !== -1) {
                    unselectedProducts.splice(unselectedProductIndex, 1);
                }
            }
            updateSelectedProducts();
        });

        $('.apply-bulk-edit-action').on('click', function() {
            $.ajax({
                url: "{{ route('products.bulk-edit') }}",
                method: 'POST',
                data: {
                    selected_products: selectedProducts,
                    unselected_products: unselectedProducts,
                    '_token': '{{ csrf_token() }}',
                    action: $('#bulkEditAction').val(),
                    tags: $('#bulkEditTags').val()
                },
                success: function(response) {
                    if (response.success) {
                        $('#bulkEditTags').val(null).trigger('change');
                        $('#bulkEditTagsModal').modal('hide');

                        swal({
                            title: "Success!",
                            text: response.message,
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                        })
                    } else {
                        let message = response.message || `Something went wrong!`;

                        swal({
                            title: "Oops!",
                            text: message,
                            type: "error",
                            confirmButtonText: 'Okay'
                        })
                    }
                }
            })
        });
        $('#bulkEditVisible').on('change', function() {
            var selectedVisibility = $(this).val();
            if (!selectedVisibility || selectedVisibility.length === 0) return;
            if (selectedProducts.length === 0 || (selectedProducts.length === 1 && selectedProducts[0] === '-1' && unselectedProducts.length === $('.product-checkbox').length)) {
                swal({
                    title: "No Products Selected",
                    text: "Please select at least one product to update visibility.",
                    type: "warning",
                    confirmButtonText: 'Okay'
                });
                $(this).val(null).trigger('change');
                return;
            }
            swal({
                title: "Are you sure?",
                text: "You are about to update visibility for " + (selectedProducts[0] === '-1' ? "all filtered products" : selectedProducts.length + " selected products"),
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, update it!",
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function(isConfirm) {
                if (isConfirm) {
                    updateBulkVisibility(selectedVisibility);
                } else {
                    $('#bulkEditVisible').val(null).trigger('change');
                }
            });
        });
        function updateBulkVisibility(visibility) {
            $.ajax({
                url: "{{ route('products.bulk-visibility') }}",
                method: 'POST',
                data: {
                    selected_products: selectedProducts,
                    unselected_products: unselectedProducts,
                    '_token': '{{ csrf_token() }}',
                    visibility: visibility
                },
                success: function(response) {
                    if (response.success) {
                        $('#bulkEditVisible').val(null).trigger('change');
                        
                        swal({
                            title: "Success!",
                            text: response.message,
                            type: "success",
                            showConfirmButton: false,
                            timer: 2000
                        });
                        
                        // Refresh the table
                        $('#product-table').DataTable().ajax.reload(function() {
                            $('input.product-checkbox').prop('checked', false);
                        }, false);
                    } else {
                        let message = response.message || `Something went wrong!`;
                        
                        swal({
                            title: "Oops!",
                            text: message,
                            type: "error",
                            confirmButtonText: 'Okay'
                        });
                    }
                },
                error: function(xhr) {
                    swal({
                        title: "Error!",
                        text: xhr.responseJSON.message || "An error occurred",
                        type: "error",
                        confirmButtonText: 'Okay'
                    });
                }
            });
        }

        $(document).on('change', '#category, #un-category', function () {
            var selected = $(this).val();
            var isAssign = $(this).attr('id') === 'category' ? 'assign' : 'unassign';
            var route = '{{ route('category.bulkUpdate') }}';
            $.ajax({
                url : route,
                type : 'POST',
                data : {
                    _token : '{{ csrf_token() }}',
                    type : isAssign,
                    categories : selected,
                    products : selectedProducts
                },success : function(resp){
                    swal({
                        title: "Success!",
                        text: resp.message,
                        type: "success",
                        showConfirmButton: false,
                        timer: 2000
                    });
                },error : function(err){
                    console.log(err);
                }
            });
        });
    });
</script>