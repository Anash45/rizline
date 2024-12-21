function calculateTotals() {

    let totals = 0;
    $('.item-qty').each(function () {
        let parentRow = $(this).closest('tr');
        let qty = Number($(this).val());
        let price = Number(parentRow.find('.item-price').text());
        let sum = qty * price;
        totals += sum;
        parentRow.find('.item-sum').text(sum);    
    })
    $('.order-total').text(totals);
}
$(document).ready(function () {

    $('.item-qty').each(function () {
        $(this).on('change keyup', function () {
            calculateTotals();
        })
    })
    // Initialize DataTable
    var table = $('#rizline').DataTable({
        orderCellsTop: true, // Ensures filter inputs don't affect the table header
        fixedHeader: true,   // Keeps the header fixed when scrolling
        columnDefs: [
            { orderable: false, targets: '_all' }, // Disable sorting for all columns
        ],
        initComplete: function () {
            // Add a search input to each column
            var api = this.api();

            // Loop through each header cell
            $('#rizline thead tr:eq(1) th').each(function (index) {
                var input = $('input', this);

                if (input.length > 0) {
                    // Attach event listeners to input fields
                    $(input).on('keyup change', function () {
                        if (api.column(index).search() !== this.value) {
                            api.column(index).search(this.value).draw();
                        }
                    });
                }
            });
            calculateTotals();
        },
    });

    // Prevent sorting when clicking on filter input fields
    $('#rizline thead .dropdown').on('click', function (e) {
        e.stopPropagation();
    });
});
