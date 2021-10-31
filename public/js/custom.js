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
