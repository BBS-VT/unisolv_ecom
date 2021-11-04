<script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $("#customer").select2({
        ajax: {
            url: "{{ route('ajax.customers') }}",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    _token: CSRF_TOKEN,
                    search: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
    });

    $("#add_product_row").click(function() {
        addProductRow();
    });

    $(".save_form_button").click(function () {
        var form = $(this).closest('form');

        // Remove price mask from values
        var price_inputs = form.find('.price_input');
        price_inputs.each(function (index, elem) {
            var price_input = $(elem);
            price_input.val(price_input.unmask());
        })

        // remove template from form
        var itemTemplate = $('#product_row_template');
        itemTemplate.remove()

        // replace all name="taxes[]" with name="taxes[rowId][]"
        $('tbody tr').each(function (index, element) {
            var row = $(element);
            var taxesInput = row.find('[name="taxes[]"]');
            taxesInput.attr('name', 'taxes[' + index + '][]');
        });

        // Submit form
        form.submit();
    })

    function calculatePercent(percent, amount) {
        var factor = Number(percent) / Number(100);
        return Number(amount) * Number(factor);
    }

    function initializeProductSselect2(elem) {
        elem.select2({
            ajax: {
                url: "{{ route('ajax.products') }}",
                type: "get",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            },
            tags: true,
            templateSelection: function (data, container) {
                $(data.element).attr('data-taxes', JSON.stringify(data.taxes));
                $(data.element).attr('data-price', data.price);
                return data.text;
            }
        });

        elem.change(function() {
            var element = $(this);
            var selectedOption = element.find(':selected');
            var taxesSelect = element.closest('tr').find('[name="taxes[]"]');
            var priceInput = element.closest('tr').find('.price_input');

            // Set selected taxes from product
            var taxIds = [];
            var taxes = selectedOption.data('taxes');
            taxes.forEach(tax => {
                taxIds.push(tax.tax_type_id);
            });
            taxesSelect.val(taxIds);
            taxesSelect.trigger('change');

            // Set product price for price input
            priceInput.val(selectedOption.data('price'));
            priceInput.focusout();

            calculateRowPrice();
        });
    }

    function initializeTaxSelect2(elem) {
        elem.select2({
            placeholder: "{{ __('global.select_taxes') }}",
        });
    }

    function calculateRowPrice() {
        var subTotal = 0;
        var taxes = {};
    }

</script>
