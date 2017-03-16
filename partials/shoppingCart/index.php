<?php
include("../../main_oauth.php");
?>
<div id="prod-top" class="prod-top-img col-md-12">
 <div class="cart-txt-shop">
    <h3>
         <?php echo _l('shopping_cart');?> 
    </h3>
  </div>
</div>


    <div class="col-md-12 content-backcolor">
        <div class="col-md-8">

            <!-- items -->
            <table class="table tab-shop">

                <!-- header -->
                <tr class="well">
                    <td><b><?php echo _l('item');?> </b></td>
                    <td class="tdCenter"><b><?php echo _l('quantity');?> </b></td>
                    <td class="tdRight"><b><?php echo _l('price');?> </b></td>
                    <td />
                </tr>

                <!-- empty cart message -->
                <tr ng-hide="cart.getTotalCount() > 0" >
                    <td class="tdCenter" colspan="4">
                        <?php echo _l('your_cart_is_empty');?> 
                    </td>
                </tr>

                <!-- cart items -->
                <tr ng-repeat="item in cart.items | orderBy:'name'">
                    <td>
                    	{{item.name}} 
                    </td>
                    <td class="tdCenter" nowrap>
                      <div class="input-append">
                        <!-- use type=tel instead of  to prevent spinners -->
                        
                        <button 
                            class="btn btn-inverse" type="button" 
                            ng-disabled="item.quantity <= 1"
                            ng-click="cart.addItem(item.code, item.name, item.price, -1)">-</button>
                        <input
                            class="col-md-2 text-center quantity-prod" type="tel" 
                            ng-model="item.quantity" 
                            ng-change="cart.saveItems()" />    
                            <button 
                            class="btn btn-inverse" type="button" 
                            ng-disabled="item.quantity >= 1000"
                            ng-click="cart.addItem(item.code, item.name, item.price, +1)">+</button>
                            
                      </div>
                    </td>
                    <td class="tdRight">{{item.price * item.quantity | currency}}</td>
                    <td class="tdCenter" title="remove from cart">
                        <a href="" ng-click="cart.addItem(item.code, item.name, item.price, -10000000)" >
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- footer -->
                <tr class="well">
                    <td><!-- <b>Total</b> --> </td>
                    <td class="tdCenter"><b><!-- {{cart.getTotalCount()}} --><?php echo _l('tax');?></b></td>
                    <td class="tdRight"><b>{{cart.getTotalTax() | currency}}</b></td>
                    <td />
                </tr>
                <tr class="well">
                    <td><!-- <b>Total</b> --> </td>
                    <td class="tdCenter"><b><!-- {{cart.getTotalCount()}} --><?php echo _l('shipping');?></b></td>
                    <td class="tdRight"><b>{{cart.getTotalShipping() | currency}}</b></td>
                    <td />
                </tr>
                <tr class="well">
                    <td><!-- <b>Total</b> --> </td>
                    <td class="tdCenter"><b><!-- {{cart.getTotalCount()}} --><?php echo _l('total');?></b></td>
                    <td class="tdRight"><b>{{cart.getTotalPrice() + cart.getTotalTax() + cart.getTotalShipping()  | currency}}</b></td>
                    <td />
                </tr>
            </table>
        </div>

        <!-- buttons -->
        <div class="col-md-4">
            <p class="text-info">
                <button 
                    class="btn btn-block" 
                    onclick="window.location.href='#store'">
                    <i class="icon-chevron-left" /> <?php echo _l('back_to_store');?>
                </button>
                <button 
                    class="btn btn-block btn-danger" 
                    ng-click="cart.clearItems()" 
                    ng-disabled="cart.getTotalCount() < 1" >
                    <i class="icon-trash icon-white" /> <?php echo _l('clear_cart');?>
                </button>
            </p>

            <p class="text-info">
                <button
                    class="btn btn-block btn-primary"
                    ng-click="cart.checkout('PayPal')"
                    ng-disabled="cart.getTotalCount() < 1">
                    <i class="icon-ok icon-white" />  <?php echo _l('check_out_with_paypal');?> 
                </button>
                
            </p>

            <br /><br />

            <p class="text-info">
                <button 
                    class="btn btn-block btn-link"
                    ng-click="cart.checkout('PayPal')"
                    ng-disabled="cart.getTotalCount() < 1" >
                    <img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="<?php echo _l('check_out_with_paypal');?> "/>
                </button>     
                  
            </p>
        </div>
    </div>
