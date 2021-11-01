$(document).ready(function() {
    // Toggle product status
    $(".updateProductStatus").click(function (){
        var status = $(this).text();
        var product_id = $(this).attr("product_id");

        $.ajax({
            type:'post',
            url: '/update-product-status',
            data:{status:status,product_id:product_id},
            success: function (resp){
                location.reload();
            },
            error:function() {
                // TODO: better error handling
                alert("Error");
            }
        })
    });
});

$(document).ready(function() {
    // Toggle Category status
    $(".updateCategoryStatus").click(function (){
        var status = $(this).text();
        var category_id = $(this).attr("category_id");

        $.ajax({
            type:'post',
            url: '/update-category-status',
            data:{status:status,category_id:category_id},
            success: function (resp){
                location.reload();
            },
            error:function() {
                // TODO: better error handling
                alert("Error");
            }
        })
    });
});

$(document).ready(function() {
    // Toggle Customer status
    $(".updateCustomerStatus").click(function (){
        var status = $(this).text();
        var customer_id = $(this).attr("customer_id");

        $.ajax({
            type:'post',
            url: '/update-customer-status',
            data:{status:status,customer_id:customer_id},
            success: function (resp){
                location.reload();
            },
            error:function() {
                // TODO: better error handling
                alert("Error");
            }
        })
    });
});

(document).ready(function(){

    // Sweet alert delete confirmation
    $('.delete-confirm').on('click', function (event) {
        event.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This record and it`s details will be permanantly deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Delete!',
            focusConfirm: false,
            focusCancel: false,
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        })
    });

    // Sweet alert delete confirmation
    $('.alert-confirm').on('click', function (event) {
        event.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: $(this).data('alert-title'),
            text: $(this).data('alert-text'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#308AF3',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Okay!',
            focusConfirm: false,
            focusCancel: false,
        }).then((result) => {
            if (result.value) {
                window.location.href = url;
            }
        })
    });

});
