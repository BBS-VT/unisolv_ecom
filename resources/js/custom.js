function setupPriceInput(currency) {
    if (!currency) return false;

    // Price format
    if(currency.swap_currency_symbol) {
        var settings = {
            prefix: '',
            centsSeparator: currency.thousand_separator,
            thousandsSeparator: currency.decimal_separator,
            suffix: currency.symbol
        }
    } else {
        var settings = {
            prefix: currency.symbol,
            centsSeparator: currency.thousand_separator,
            thousandsSeparator: currency.decimal_separator,
            suffix: '',
        }
    }
    $('.price_input').priceFormat(settings);
}

window.setupPriceInput = setupPriceInput;