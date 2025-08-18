@extends('layouts.app')

@section('title', 'Print Barcodes Preview')
@section('header-button')
    <button id="print-btn" class="btn btn-primary me-2"><i class="bx bx-printer"></i> Print</button>
@endsection

@section('content')
    @push('styles')
        <style id="tickets-css">
            @page {
                size: A4;
                margin-top: 15.3mm;
                margin-bottom: 15.3mm;
                margin-left: 4.8mm;
                margin-right: 4.8mm;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                flex-wrap: wrap;
                justify-content: center;
            }

            .pages {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .page {
                display: grid;
                grid-template-columns: repeat(4, 48.6mm);
                grid-template-rows: repeat(9, 29.6mm);
                column-gap: 2mm;
                padding: 0;
                box-sizing: border-box;
                page-break-after: always;
            }

            .ticket-container {
                border: 1px solid #ddd;
                border-radius: 2mm;
                display: block;
                font-size: 12px;
                overflow: hidden;
                padding: 4px;
                color: #000
            }

            .page-content .page-header {
                margin: 0 34mm;
            }

            .brand-name {
                font-size: 14px;
                margin: 0;
                font-weight: bold;
            }

            .description-container {
                display: flex;
                justify-content: space-between
            }

            .description-container p {
                font-size: 12px;
                margin: 0;
            }

            .variant-info {
                display: flex;
                justify-content: space-between;
            }

            .variant-info p {
                margin: 0;
            }

            .barcode-container {
                width: 100%;
                display: flex;
                gap: 4px;
            }

            .barcode-container img {
                width: 130px
            }

            .barcode-left-section p {
                margin: 0;
            }

            .barcode-right-section {
                width: calc(100% - 130px);
            }

            .mrp {
                font-size: 14px;
                font-weight: bold;
            }

            .size-name {
                text-align: center;
                font-weight: bold;
                font-size: 16px;
                margin-top: 6px;
            }

            .department-name {
                text-align: center;
            }
        </style>
    @endpush

    <div class="page-content">
        <div class="pages">
            @foreach (array_chunk($barcodes, 36) as $pageIndex => $barcode)
                <div class="page">
                    @foreach ($barcode as $index => $item)
                        <div @class([
                            'ticket-container',
                            'bg-success-subtle' => $pageIndex == 0 && $index == 0,
                        ]) data-index="{{ $index }}">
                            <h6 class="brand-name">{{ Str::limit($item['brand_name'], 14) }}</h6>
                            <div class="description-container">
                                <p>{{ Str::limit(strtoupper($item['short_description']), 14) }}</p>
                                <p>{{ $item['random_digits'] }}</p>
                            </div>
                            <div class="variant-info">
                                <p>{{ strtoupper($item['color']) }}</p>
                                <p>{{ strtoupper($item['product_type_short_name']) }}</p>
                                <p class="mrp">£{{ $item['mrp'] }}</p>
                            </div>
                            <div class="barcode-container">
                                <div class="barcode-left-section">
                                    <img src="data:image/png;base64,{{ $item['barcode'] }}" alt="Barcode">
                                    <p>{{ $item['product_code'] }}{{ substr($item['department'], 0, 1) }}</p>
                                </div>
                                <div class="barcode-right-section">
                                    <div class="size-name">{{ strtoupper($item['size']) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>
    </div>

    @push('scripts')
        <script>
            $(function() {
                let selectedIndex = null;
                let barcodes = [];

                // Make the first barcode selected by default
                let firstBarcode = $('.ticket-container').first();
                firstBarcode.addClass('bg-success-subtle');
                selectedIndex = firstBarcode.data('index');

                $(document).on('click', '.ticket-container', function() {
                    $('.ticket-container').removeClass('bg-success-subtle');
                    $(this).addClass('bg-success-subtle');

                    selectedIndex = $(this).data('index');
                });

                $('#print-btn').click(function() {
                    if (selectedIndex === null) {
                        alert('Please select a barcode first.');
                        return;
                    }

                    openPrintWindowFromIndex(selectedIndex);
                });
            });

            function openPrintWindowFromIndex(index) {
                let tickets = $('.pages').find('.ticket-container').toArray();

                var firstPart = tickets.slice(0, index);
                var secondPart = tickets.slice(index);

                var firstTicketsPage = createTicketsPage(firstPart);
                var secondTicketsPage = createTicketsPage(secondPart);

                var combinedHtml = `
                    ${firstTicketsPage.length > 0 ? firstTicketsPage.join('') : ''}
                    ${secondTicketsPage.length > 0 ? secondTicketsPage.join('') : ''}
                `;

                var styles = $('#tickets-css').html();

                var printWindowContent = `
                <html>
                <head>
                    <title>Print Barcode</title>
                    <style>${styles}</style>
                </head>
                <body>
                    <div>${combinedHtml}</div>
                </body>
                </html>
                `;

                var iframe = document.createElement('iframe');
                iframe.style.position = 'absolute';
                iframe.style.width = '0px';
                iframe.style.height = '0px';
                iframe.style.border = 'none';

                document.body.appendChild(iframe);

                var iframeDoc = iframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(printWindowContent);
                iframeDoc.close();

                iframe.onload = function() {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                };

                iframe.contentWindow.onafterprint = function() {
                    document.body.removeChild(iframe);
                    window.location.href = `{{ route('save.barcodes') }}`;
                };
            }

            function createTicketsPage(elements) {
                let chunkedBarcodes = [];

                // Chunk the elements array into groups of 36
                for (let i = 0; i < elements.length; i += 36) {
                    chunkedBarcodes.push(elements.slice(i, i + 36));
                }

                // Create an array to hold all the generated pages
                let allPages = [];

                chunkedBarcodes.forEach(function(barcodePage, pageIndex) {
                    let pageDiv = $('<div class="page"></div>'); // Create a page div

                    // Append each barcode item to the page
                    barcodePage.forEach(function(item, index) {
                        // Assuming `item` is the barcode content to be appended
                        pageDiv.append($(item).clone());
                    });

                    // Append the constructed page to the array of all pages
                    allPages.push(`<div class="page">${$($(pageDiv).clone()).html()}</div>`);
                });

                // Return all the pages as an array
                return allPages;
            }
        </script>
    @endpush
@endsection
