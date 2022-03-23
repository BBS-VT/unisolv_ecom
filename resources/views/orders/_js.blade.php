<script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var company_currency = '4';
    var globalCustomer = 0;
    var globalDiscount = 0;

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
        templateSelection: function (data, container) {
            $(data.element).attr('data-currency', JSON.stringify(data.currency));
            return data.text;
        }
    });

    $("#customer").change(function() {
        setupCustomer();
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

    function setupCustomer() {
        var customer_id = $("#customer").val();
        globalCustomer = customer_id;
        var currency = $('#customer').find(':selected').data('currency');

        // Setup currency
        window.sharedData.company_currency = currency;
        setupPriceInput(window.sharedData.company_currency);
    }

    function initializeProductSselect2(elem) {
        var customer_id = globalCustomer;
        elem.select2({
            ajax: {
                url: "{{ route('ajax.products') }}",
                type: "get",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        customer_id: globalCustomer,
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
                if(data.DiscountPercentage !== null) {
                    $(data.element).attr('data-discount', data.DiscountPercentage);
                } else {
                    $(data.element).attr('data-discount', '0');
                }
                if(data.UnitPrice !== null && data.UnitPrice > 0) {
                    $(data.element).attr('data-price', data.UnitPrice);
                } else {
                    $(data.element).attr('data-price', data.price);
                }
                if(data.discount !== null) {
                    globalDiscount = data.discount;
                }

                return data.text;
            }
        });

        elem.change(function() {
            var element = $(this);
            var selectedOption = element.find(':selected');
            var taxesSelect = element.closest('tr').find('[name="taxes[]"]');
            var priceInput = element.closest('tr').find('.price_input');
            var discountInput = element.closest('tr').find('[name="discount[]"]');

            // Set selected taxes from product
            var taxIds = [];
            var taxes = selectedOption.data('taxes');
            taxes.forEach(tax => {
                taxIds.push(tax.tax_type_id);
            });
            taxesSelect.val(taxIds);
            taxesSelect.trigger('change');

            // Set product discount if set
            discountInput.val(selectedOption.data('discount'));

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

        $('tbody tr').each(function(index, element) {
            var row = $(element);

            // If the row is template just continue
            if(row.attr('id') === 'product_row_template') return;

            // quantity
            var quantity = Number(row.find('[name="quantity[]"]').val());
            //console.log(quantity)

            // price
            var price = Number(row.find('.price_input').unmask()) / 100;


            // amount
            var amount = (quantity * price);


            // Calculate taxes
            var totalTaxAmount = Number(0);
            var selected_taxes = row.find('[name="taxes[]"]').find(':selected');
            selected_taxes.each(function (index, tax) {
                var percent = $(tax).data('percent');
                var taxAmount = calculatePercent(percent, amount);
                //console.log("taxAmount", taxAmount);
                totalTaxAmount += Number(taxAmount);
            });

            // Add tax amount to Item Total
            amount = Number(amount) + Number(totalTaxAmount) - Number(totalTaxAmount);

            // discount
            var discount = Number(row.find('[name="discount[]"]').val());
            //console.log("globalDiscount", globalDiscount);
            //console.log("discount", discount);

            // calculate discount
            if(!isNaN(discount) && discount != undefined && discount != 0) {
                var discountAmount = calculatePercent(discount, amount);

                    if(globalDiscount !== '0.01') {

                        if (discount <= globalDiscount) {

                            amount = Number(amount) - Number(discountAmount);
                            $('#add_product_row').attr('disabled', false);
                            $('#save_form_button').attr('disabled', false);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Discount cannot exceed ' + globalDiscount + ' % for this item',
                            })
                            $('#add_product_row').attr('disabled', true);
                            $('#save_form_button').attr('disabled', true);
                        }

                    } else {
                        amount = Number(amount) - Number(discountAmount);

                        Number(row.find('[name="discount[]"]').attr("readonly", "true"));

                        $('#add_product_row').attr('disabled', false);
                        $('#save_form_button').attr('disabled', false);
                    }

            } else if (discount == 0 && globalDiscount == 0.01) {
                var discountAmount = calculatePercent(discount, amount);

                amount = Number(amount) - Number(discountAmount);

                Number(row.find('[name="discount[]"]').attr("readonly", "true"));
                $('#add_product_row').attr('disabled', false);
                $('#save_form_button').attr('disabled', false);

            }

            // Add Item Total to Sub Total
            subTotal += Number(amount);

            var amountPrice = Number(amount) ;
            //console.log("subTotal", subTotal);

            // Set price input value
            row.find('.amount_price').val(amountPrice.toFixed(2));
            row.find('.amount_price').focusout();
        });

        calculateTotalPrice(subTotal, taxes);
    }

    function calculateTotalPrice(subTotal, taxes) {
        // Total value
        total = 0;
        total += subTotal;

        // Set subtotal value
        subtotal = Number(subTotal);

        $('#sub_total').val(subtotal.toFixed(2));

        // total taxes
        var total_taxes = $('#total_taxes').find(':selected');
        total_taxes.each(function (index, tax) {
            var taxName = $(tax).text();
            var percent = $(tax).data('percent');
            var taxAmount = calculatePercent(percent, subTotal);

            // push tax to taxes array
            if(taxes[taxName]) {
                taxes[taxName] += Number(taxAmount);
            } else {
                taxes[taxName] = Number(taxAmount);
            }
        });

        // Display total tax list
        $('.total_tax_list').empty();
        for (var [name, amount] of Object.entries(taxes)) {
            var template = '<div class="dflex align-items-center mb-3">' +
                '<div class="h6 mb-0 w-50">' +
                '  <strong class="text-muted">' + name + '</strong>' +
                '</div>' +
                '<div class="ml-auto h6 mb-0">' +
                '  <input type="text" class=price_input price-text w-100 fs-inherit" value="'+ Number(amount).toFixed(2) +'" disabled>' +
                '</div>' +
            '</div>';

            $('.total_tax_list').append(template);

            total = Number(total) + Number(amount);
        }

        // total discount
        var total_discount = $('#total-discount').val();
        if(total_discount != undefined && total_discount != 0) {
            total_discount = parseFloat(total_discount);
            var discountAmount = calculatePercent(total_discount, subTotal)
            total = Number(total) - Number(discountAmount)
        }

        $('#grand_total').val(Number(total).toFixed(2));
        setupPriceInput(window.sharedData.company_currency);
    }

    function initializePriceListener() {
        $(".priceListener").change(function() {
            calculateRowPrice()
        });
    }

    function addProductRow() {
        var productItems = $('#items');
        var template = $('#product_row_template')
            .clone()
            .removeAttr('id')
            .removeClass('d-none');
        productItems.append(template);

        var product_select = template.find('[name="product[]"]');
        initializeProductSselect2(product_select);

        var tax_select = template.find('[name="taxes[]"]');
        initializeTaxSelect2(tax_select);

        initializePriceListener();
        calculateRowPrice();
    }

    function removeRow(elem) {
        $(elem).closest('tr').remove();
        calculateRowPrice();
    }

    function validateForm() {
        $('tbody tr').each(function(index, element) {
            var row = $(element);
            var product = row.find('[name="product[]"]')
        });
    }

</script>
