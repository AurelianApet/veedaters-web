$(document).ready(function(){
    
    // user manage filter drop down
        $("#user_manage_filter" ).change(function() {
            $('#user-manage-filter-form').submit();
        });
        
    
    // user/subscription
    $("#stripePlans" ).change(function() {
          if($(this).val() == '')
          {
              $('#planError').show();
              setTimeout(function(){ $('#planError').hide(); }, 3000);
          }
          else
          {
              $('#planError').hide();
              $('#payment-form').show();
              
              //var stripe = Stripe('pk_live_XoLVgXQfnK1L7wrP2KrVXjVz');
              var stripe = Stripe('pk_test_tZGIOK0ezwLGPerGW5xnKOmP');
              
                // Create an instance of Elements
                var elements = stripe.elements();

                // Custom styling can be passed to options when creating an Element.
                var style = {
                  base: {
                    color: '#32325d',
                    lineHeight: '18px',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    border:'solid 1px',
                    fontSize: '16px',
                    '::placeholder': {
                      color: '#aab7c4'
                    }
                  },
                  invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                  }
                };

                // Create an instance of the card Element
                var card = elements.create('card', {style: style});

                // Add an instance of the card Element into the `card-element` <div>
                card.mount('#stripe-card-element');

                // Handle real-time validation errors from the card Element.
                card.addEventListener('change', function(event) {
                  var displayError = document.getElementById('card-errors');
                  if (event.error) {
                    displayError.textContent = event.error.message;
                  } else {
                    displayError.textContent = '';
                  }
                });

                // Handle form submission
                var form = document.getElementById('payment-form');
                form.addEventListener('submit', function(event) {
                  event.preventDefault();

                  stripe.createToken(card).then(function(result) {
                    if (result.error) {
                      // Inform the user if there was an error
                      var errorElement = document.getElementById('card-errors');
                      errorElement.textContent = result.error.message;
                    } else {
                      // Send the token to your server                      
                      if(result.token.id !='')
                      {
                          $('.custom-loader').show();
                      }
                      var base_url = $('#base_url').val();
                      $.post( base_url+'/user/subscription', 
                            { 
                                'User[token]': result.token.id,
                                'User[plan]': $('#stripePlans').val(),
                                'User[user_id]': $('#user_id').val(),
                            }, function( data ) {
                                $('.custom-loader').hide();
                                location.reload(); 
                        });
                    }
                  });
                });

          }
    });
    
    
    // active/deactive user from single user page on admin end
    $( ".deactivate-user" ).click(function() {
        var base_url = $('#base_url').val();
        $.post( base_url+'/user/user-update?id='+$(this).data('user-id'), 
            { 
                'User[user_id]': $(this).data('user-id'),
                'User[is_active]': $(this).data('user-status'),
            }, function( data ) {
              var filterData = $.parseJSON(data);
              if(filterData.status == 0)
              {
                  $('#deactivate-user').css("display","none");
                  $('#activate-user').css("display","block");
              }
              if(filterData.status == 1)
              {
                  $('#deactivate-user').css("display","block");
                  $('#activate-user').css("display","none");
              }
          });
    });
    
    
    // delete user photos
    $( ".delete-user-photo" ).click(function() {
        var confirmMsg = confirm("Do you really want to delete this photo ?");
        if (confirmMsg == true) {
            var base_url = $('#base_url').val();
            $.post( base_url+'/user/photo-delete', 
                { 
                    'User[photo_id]': $(this).data('photoid'),
                    'User[user_id]': $('#user_id').val(),
                    'User[action]': 'delete',
                }, function( data ) {
                    location.reload();
            });
        }
        
    });
    
    
    
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
    
    
    // send message on single user page
    $("textarea.single-user-message").keypress(function(event) {
        if (event.which == 13) {
            var base_url = $('#base_url').val();
            $.post( base_url+'/message/create', 
                { 
                    'Message[message_subject]': $(this).val(),
                    'Message[message_body]': $(this).val(),
                    'Message[recipient_id]': $(this).data('recipient-id'),
                    'Message[message_parent_id]': 0,
                    'Message[message_type]': 2,
                }, function( data ) {
            });
            $('#user-message-body-section').append('<div class="clearfix"><blockquote class="me pull-left">'+$(this).val()+'</blockquote></div>');
            $(this).val('');
        }
    });
    
    
    // datatable IDs
    //$('#user-orders').DataTable();
    
})


function sendMessage()
{
    $.ajax({
    type: "POST",
    url: "send-message",
    data:{user_id:$('#sender_id').val(),message:$('#message').val()},
    beforeSend: function(){
            //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
            $('#message-sent-success').show();
            setTimeout(function(){ 
                $('#message-sent-success').hide();
                location.reload();
            }, 3000);
    }
    });
}

function sendMessageFunction()
{   
    $('.send_message_container').toggle('slow');
}


/************************************** */

 // search user to send message
 $("#search_user_field").keyup(function(){
    $.ajax({
    type: "POST",
    url: "search-user",
    data:'username='+$('#search_user_field').val(),
    beforeSend: function(){
            //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
            $("#suggesstion-box").show();
            $("#suggesstion-box").html(data);
            $("#add-shop").css("background","#FFF");
    }
    });
});$(document).ready(function(){
    
    // active/deactive shop from single shop page on admin end
    $( ".deactivate-shop" ).click(function() {
        var base_url = $('#base_url').val();
        $.post( base_url+'/manage-shop/shop-update?id='+$(this).data('shop-id'), 
            { 
                'Shop[shop_id]': $(this).data('shop-id'),
                'Shop[isactive]': $(this).data('shop-status'),
            }, function( data ) {
              var filterData = $.parseJSON(data);
              if(filterData.status == 0)
              {
                  $('#deactivate-shop').css("display","none");
                  $('#activate-shop').css("display","block");
              }
              if(filterData.status == 1)
              {
                  $('#deactivate-shop').css("display","block");
                  $('#activate-shop').css("display","none");
              }
          });
    });
    
    // user manage filter drop down
        $("#user_manage_filter" ).change(function() {
            $('#user-manage-filter-form').submit();
        });
        
    // refunding money     
    $(".refundurl").click(function(e){ if(confirm("Are you sure you want to refund?")){
        
    }
    else{
        return false;
    } });

    
    // active/deactive product from single product page on admin end
    $( ".deactivate-product" ).click(function() {
        var base_url = $('#base_url').val();
        $.post( base_url+'/manage-food/product-update?id='+$(this).data('product-id'), 
            { 
                'Product[product_id]': $(this).data('product-id'),
                'Product[isactive]': $(this).data('product-status'),
            }, function( data ) {
              var filterData = $.parseJSON(data);
              if(filterData.status == 0)
              {
                  $('#deactivate-product').css("display","none");
                  $('#activate-product').css("display","block");
              }
              if(filterData.status == 1)
              {
                  $('#deactivate-product').css("display","block");
                  $('#activate-product').css("display","none");
              }
          });
    });
    
    // active/deactive user from single user page on admin end
    $( ".deactivate-user" ).click(function() {
        var base_url = $('#base_url').val();
        $.post( base_url+'/user/user-update?id='+$(this).data('user-id'), 
            { 
                'User[user_id]': $(this).data('user-id'),
                'User[is_active]': $(this).data('user-status'),
            }, function( data ) {
              var filterData = $.parseJSON(data);
              if(filterData.status == 0)
              {
                  $('#deactivate-user').css("display","none");
                  $('#activate-user').css("display","block");
              }
              if(filterData.status == 1)
              {
                  $('#deactivate-user').css("display","block");
                  $('#activate-user').css("display","none");
              }
          });
    });
    
    
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
    
    
    // // send message on single user page
    // $("textarea.single-user-message").keypress(function(event) {
    //     if (event.which == 13) {
    //         var base_url = $('#base_url').val();
    //         $.post( base_url+'/message/create', 
    //             { 
    //                 'Message[message_subject]': $(this).val(),
    //                 'Message[message_body]': $(this).val(),
    //                 'Message[recipient_id]': $(this).data('recipient-id'),
    //                 'Message[message_parent_id]': 0,
    //                 'Message[message_type]': 2,
    //             }, function( data ) {
    //         });
    //         $('#user-message-body-section').append('<div class="clearfix"><blockquote class="me pull-left">'+$(this).val()+'</blockquote></div>');
    //         $(this).val('');
    //     }
    // });
    
    
    // delete shop permanently 
    $( "#delete-shop" ).click(function() {
        var base_url = $('#base_url').val();
        $.post( base_url+'/manage-shop/delete?id='+$(this).data('shop-id'), 
            { 
                'Shop[shop_id]': $(this).data('shop-id'),
                'Shop[action]': "delete",
            }, function( data ) {
              var filterData = $.parseJSON(data);
              if(filterData.status == 0)
              {
                  $('#deactivate-shop').css("display","none");
                  $('#activate-shop').css("display","block");
              }
              if(filterData.status == 1)
              { 
                  $('#deactivate-shop').css("display","block");
                  $('#activate-shop').css("display","none");
              }
          });
    });
    
    
    
    // auto suggest for featured shop
     $("#add-shop").keyup(function(){
            $.ajax({
            type: "POST",
            url: "search-shop",
            data:'shop_title='+$(this).val(),
            beforeSend: function(){
                    //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(data);
                    $("#add-shop").css("background","#FFF");
            }
            });
    });
    
    
    
    
    // search user to send message
     $("#search_user_field").keyup(function(){
            $.ajax({
            type: "POST",
            url: "search-user",
            data:'username='+$('#search_user_field').val(),
            beforeSend: function(){
                    //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(data);
                    $("#add-shop").css("background","#FFF");
            }
            });
    });
    
    
    // datatable IDs
    $('#order_pickup').DataTable({"pageLength": 50});
    $('#order_delivery').DataTable({"pageLength": 50});
    $('#order_shipping').DataTable({"pageLength": 50});
    $('#order_completed').DataTable({"pageLength": 50});
    $('#product-order').DataTable({"pageLength": 50});
    $('#admin_current_transaction').DataTable({"pageLength": 50});
    $('#admin_transaction').DataTable({"pageLength": 50});
    $('#admin_previous_transaction').DataTable({"pageLength": 50});
    $('#shop_order_history').dataTable( {"pageLength": 50} );
    $('#user-orders').DataTable({"pageLength": 50});
    
})


function selectShop(val,id) {
    $('#featured_shop_id').val(id);
    $("#add-shop").val(val.replace("-"," "));
    $("#suggesstion-box").hide();
}

function selectUser(val,id) {
    $('#sender_id').val(id);
    $('#user_id').val(id);
    // get user orders and make a drop down
    $.ajax({
    type: "POST",
    url: "user-orders",
    data:{user_id:id,action:'user_orders'},
    beforeSend: function(){
            //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
            $('#user-order-list').html(data);
    }
    });
    
    $("#search_user_field").val(val.replace("-"," "));
    $("#suggesstion-box").hide();    
}


function featuredShop(status) {
    $.ajax({
    type: "POST",
    url: "featured-shop",
    data:{shop_id:$('#featured_shop_id').val(),status:status},
    beforeSend: function(){
            //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
            location.reload();
    }
    });
}

function userRefund() {
    $.ajax({
    type: "POST",
    url: "user-refund",
    data:{user_id:$('#user_id').val(),status:"refund"},
    beforeSend: function(){
            //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
            location.reload();
    }
    });
}



function removeFeaturedShop(status,id) {
    $.ajax({
    type: "POST",
    url: "featured-shop",
    data:{shop_id:id,status:status},
    beforeSend: function(){
            //$("#add-shop").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
    },
    success: function(data){
            location.reload();
    }
    });
}


function deliveryEligibility(status,id) {
      $('.changeStatusDeliveryEligibility').show();
      $('.defaultStatusDeliveryEligibility').hide();
}

function deliveryOption(status,id) {
      $('.changeStatusDeliveryOption').show();
      $('.defaultStatusDeliveryOption').hide();
}

function changeDeliveryCharges(id) {
      $('.delivery-fee-row-'+id).find('.changeStatusDeliveryCharge').show();
      $('.delivery-fee-row-'+id).find('.changeStatusDeliveryChargeLink').hide();
}




function setStatusDeliveryEligibility(status)
{
    $.ajax({
    type: "POST",
    url: "delivery-eligibility",
    data:{product_id:$('#product_id').val(),status:status},
    beforeSend: function(){
    },
    success: function(data){
            location.reload();
    }
    });
}

function setStatusDeliveryOption(status)
{
    $.ajax({
    type: "POST",
    url: "delivery-option",
    data:{product_id:$('#product_id').val(),status:status},
    beforeSend: function(){
    },
    success: function(data){
        location.reload();
    }
    });
}




function setDeliveryCharge(val,id)
{
    $.ajax({
    type: "POST",
    url: "delivery-charge",
    data:{town_id:id,delivery_charges:val},
    beforeSend: function(){
    },
    success: function(data){
            location.reload();
    }
    });
}

