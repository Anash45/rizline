$(document).ready(function () {


    let currency = 'usd';
    let prevCurrency = 'usd';




    // Initialize DataTable
    var table = $('#rizline').DataTable({
        paging: true,
        orderCellsTop: true,
        fixedHeader: true,
        pageLength: 25
    });

    // Attach event listener for the 'draw' event
    table.on('draw', function () {
        console.log('Page changed or table redrawn');
        // Call your custom function here
        applyCurrency();
    });

    // Handle item filter
    $('.item-filter').on('change', function () {
        let selectedItems = [];

        // Gather selected item values
        $('.item-filter:checked').each(function () {
            selectedItems.push($(this).val());
        });

        // Update DataTable search
        if (selectedItems.length > 0) {
            table.column(2).search(selectedItems.join('|'), true, false).draw();
        } else {
            table.column(2).search('').draw();
        }
    });

    // Object to store non-zero quantities
    let productData = {};

    // Function to update product data
    function updateProductData(row) {
        const productId = parseInt($(row).find('.item-id').text());
        const price = parseFloat($(row).find('.item-price').text());
        const qty = parseInt($(row).find('.item-qty').val()) || 0;

        if (qty > 0) {
            productData[productId] = { price, qty };
        } else {
            delete productData[productId];
        }
        updateHiddenInputs();
    }

    // Function to calculate the total
    function calculateTotal() {
        console.log(productData);
        let total = 0;
        for (const productId in productData) {
            const { price, qty } = productData[productId];
            let item_total = price * qty;
            total += price * qty;
            $('#item_' + productId).find('.item-sum').text(item_total.toFixed(2));
        }
        $('.order-total').text(total.toFixed(2));
    }

    // Event listener for quantity changes
    $('#rizline tbody').on('input', '.item-qty', function () {
        const row = $(this).closest('tr');
        updateProductData(row);
        calculateTotal();
    });

    // // Initialize product data for rows with pre-filled quantities
    // $('#rizline tbody tr').each(function () {
    //     const qty = parseInt($(this).find('.item-qty').val()) || 0;
    //     if (qty > 0) {
    //         updateProductData($(this));
    //     }
    // });

    // Initial total calculation

    function updateHiddenInputs() {
        // Clear existing hidden inputs
        $('input[name="item_ids[]"]').remove();
        $('input[name="quantities[]"]').remove();
        $('input[name="prices[]"]').remove();

        // Populate hidden inputs with productData
        for (const productId in productData) {
            const { price, qty } = productData[productId];

            // Create and append hidden inputs
            $('<input>', {
                type: 'hidden',
                name: 'item_ids[]',
                value: productId
            }).appendTo('form');

            $('<input>', {
                type: 'hidden',
                name: 'quantities[]',
                value: qty
            }).appendTo('form');

            $('<input>', {
                type: 'hidden',
                name: 'prices[]',
                value: price
            }).appendTo('form');
        }
    }

    calculateTotal();


    function applyCurrency(selectedCurrency) {
        console.log(selectedCurrency, prevCurrency);

        // Set the currency symbol
        let rateCurrency = 'eur';
        if (selectedCurrency === 'usd') {
            rateCurrency = 'eur'
            $('.price-sign').html('&dollar;');
        } else if (selectedCurrency === 'eur') {
            rateCurrency = 'usd'
            $('.price-sign').html('&euro;');
        }

        // Get all prices
        const prices = $('.price-amount');

        if (selectedCurrency !== prevCurrency) {
            console.log('1');
            // Fetch conversion rate
            const apiUrl = `https://v6.exchangerate-api.com/v6/b86433b2ed93e064a73e37c2/latest/${rateCurrency}`;
            $.get(apiUrl, function (response) {
                console.log(response);
                if (response && response.conversion_rates) {
                    const rate = response.conversion_rates[selectedCurrency.toUpperCase()];
                    if (rate) {
                        // Apply conversion
                        prices.each(function () {
                            const currentPrice = parseFloat($(this).text());
                            if (!isNaN(currentPrice)) {
                                const convertedPrice = (currentPrice * rate).toFixed(2);
                                $(this).text(convertedPrice); // Update the price
                            }
                        });
                    } else {
                        console.error('Currency rate not available for', selectedCurrency);
                    }
                } else {
                    console.error('Error fetching conversion rates');
                }
            });
            prevCurrency = selectedCurrency;
        }
    }
    const currencySelect = $('#current_currencyy');

    // Restore the saved currency value on page load
    const savedCurrency = localStorage.getItem('selectedCurrency');
    if (savedCurrency) {
        currencySelect.val(savedCurrency);
        applyCurrency(savedCurrency);
        currency = savedCurrency;
    } else {
        localStorage.setItem('selectedCurrency', currency);
        applyCurrency(currency);
    }




    // Save the selected currency value to localStorage on change
    currencySelect.on('change', function () {
        currency = $(this).val();
        localStorage.setItem('selectedCurrency', currency);
        applyCurrency(currency);
    });
});

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()

function exportIntoExcel(order_id = 0) {
    // Create a workbook and sheet
    let table = document.getElementById('order_details');
    let wb = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });

    // Export to file
    XLSX.writeFile(wb, 'order_' + order_id + '_data.xlsx');
}
