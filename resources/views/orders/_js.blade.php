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

</script>
