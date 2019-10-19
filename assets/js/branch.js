jQuery( function($){
    var a = 'select#shipping_branch',
        b = 'input[name^="shipping_branch[0]"]',
        c = '#shipping_method_0_'

    // Live action event: On Select "delivery_method" change
    $(a).change( function () {
        console.log($(this).val());
        $.ajax({
           type : "post",
           url : wp_branch_vars.ajaxurl, // Pon aquí tu URL
           data : {
               action: "branch_update_field",
               branch_id : $(this).val()
           },
           error: function(response){
               console.log(response);
           },
           success: function(response) {
               // Actualiza el mensaje con la respuesta
               console.log(response)
               $( document.body ).trigger( 'update_checkout' );
           }
       })
        /*if($(this).val() == 'shipping-by-email' )
            $(c+e).prop("checked", true);
        else
            $(c+d).prop("checked", true);*/

    });

    // Live action event: On Shipping method change
    $( 'form.checkout' ).on( 'change', b, function() {
        console.log($(this).val());
        // If Free shipping is not selected, we change "delivery_method" slect field to "post"
        /*if( $(this).val() != f )
            $(a).val('shipping-by-post');
        else
            $(a).val('shipping-by-email');*/
    });
});
