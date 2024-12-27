$(document).ready(function () {


    let currency = 'eur';
    let prevCurrency = 'eur';




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
        applyCurrency(currency);
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
        if (selectedCurrency === 'usd') {
            $('.price-sign').html('&dollar;');
        } else if (selectedCurrency === 'eur') {
            $('.price-sign').html('&euro;');
        }

        // Get all prices
        const prices = $('.price-amount');

        if (selectedCurrency) {
            console.log('Fetching conversion rates...');
            $.ajax({
                url: 'currency_converter.php',
                type: 'GET',
                data: { base: selectedCurrency === 'usd' ? 'eur' : 'usd' }, // Base currency for conversion
                dataType: 'json',
                success: function (rates) {
                    console.log('Conversion rates:', rates);
                    const rate = rates[selectedCurrency.toUpperCase()];
                    if (rate) {
                        // Apply conversion
                        updatePrices(prices, rate, selectedCurrency);
                    } else {
                        console.error('Currency rate not available for', selectedCurrency);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching conversion rates:', error);
                }
            });
            prevCurrency = selectedCurrency;
        }
    }

    function updatePrices(prices, rate, curr) {
        prices.each(function () {
            const currentPrice = parseFloat($(this).text());
            if (!isNaN(currentPrice) && !$(this).hasClass(curr)) {
                const convertedPrice = (currentPrice * rate).toFixed(2);
                $(this).text(convertedPrice); // Update the price
                $(this).removeClass('usd');
                $(this).removeClass('eur');
                $(this).addClass(curr);
            }
        });
    }



    const currencySelect = $('#current_currency');

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

    $(document).ready(function () {
        $('th .dropdown').on('click', function (e) {
            e.stopPropagation();
        })
    })
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

$(document).ready(function () {
    // Initialize all date inputs with the 'date-inp' class
    $('.date-inp').each(function () {
        const input = $(this);

        // Get the default value from the input's value attribute
        const defaultDate = input.val(); // Use .val() to get the value, as .attr('value') might not reflect live changes

        // Initialize the datepicker with the default date
        input.datepicker({
            language: 'tr', // Set the language to Turkish
            format: 'dd MM yyyy', // Proper Turkish format: day month year
            autoclose: true, // Automatically close the datepicker after selection
            todayHighlight: true, // Highlight today's date
            weekStart: 1, // Week starts on Monday
        });

        // Set the default date if available
        if (defaultDate) {
            input.datepicker('setDate', defaultDate);
        }
    });

    $('.date-form').submit(function (e) {
        // Loop through each date input
        $('.date-inp').each(function () {
            const input = $(this);
            const dateValue = input.val();

            if (dateValue) {
                // Convert from 'dd MM yyyy' format to 'yyyy-mm-dd'
                const parts = dateValue.split(' '); // Split by space: day, month, year
                const day = parts[0];
                const month = getMonthNumber(parts[1]); // Get the numeric month from the Turkish month name
                const year = parts[2];

                if (day && month && year) {
                    // Set the new value in 'yyyy-mm-dd' format
                    const formattedDate = `${year}-${month}-${day}`;
                    input.val(formattedDate); // Update the input value
                }
            }
        });
    });

    // Helper function to map Turkish month names to numeric month values
    function getMonthNumber(monthName) {
        const months = {
            'Ocak': '01', 'Şubat': '02', 'Mart': '03', 'Nisan': '04',
            'Mayıs': '05', 'Haziran': '06', 'Temmuz': '07', 'Ağustos': '08',
            'Eylül': '09', 'Ekim': '10', 'Kasım': '11', 'Aralık': '12'
        };
        return months[monthName] || null;
    }
});
