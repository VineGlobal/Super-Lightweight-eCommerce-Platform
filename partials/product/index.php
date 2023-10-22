<?php
include("../../main_oauth.php");
?>
<div id="prod-top" class="prod-top-img col-md-12">
 <div class="cart-txt-top">
   <img ng-src="{{product.src}}" alt="{{product.name}}"/>
    <p class="text-info">
       <span class="product-name-title"> {{product.name}}:</span> {{product.description}}<br />
    </p>
  </div>
</div>
<div class="clear"></div>
    <div class="col-md-12 content-backcolor">
        <div class="col-md-8">
			 
	        <!-- product info -->
            <table class="table  tab-shop">
                <tr class="well">
                    <td class="tdRight" colspan="3" >
                        <a href="#/cart" class="cart-txt-color" title="go to shopping cart" ng-disabled="cart.getTotalCount() < 1">
                        
                            <img src="images/cart.png" alt="" />	
                            <b>{{cart.getTotalCount()}}</b> <?php echo _l('items');?>, <b>{{cart.getTotalPrice() | currency}}</b>
                            <span class="txt-cart-prod" ng-show="cart.getTotalCount(product.code) > 0"><br /><?php echo _l('this_item_is_in_the_cart');?></span>
                        </a>
                    </td>
                </tr>
                
                <tr ng-repeat="x in detail" ng-if="detail.length > 0">
	    			 <td><?php echo _l('color');?>:{{ x.color }}</td>
	   				 <td><?php echo _l('size');?>: {{ x.size }}</td>
		   			<td> <button 
		                class="btn btn-block btn-success" 
		                ng-click="cart.addItem(x.sku,x.name, x.price, 1,x.description)">
		                <i class="icon-shopping-cart icon-white" /> <?php echo _l('add_to_cart');?>
		            	</button>
	            	</td>
  				</tr>
  				
  				<tr ng-if="!detail.length">
	    			 <td colspan="2"><?php echo _l('only_one_size');?></td>
	   				<td> <button 
			                class="btn btn-block btn-success" 
			                ng-click="cart.addItem(product.code, product.name, product.price, 1,product.description)">
			                <i class="icon-shopping-cart icon-white" /> <?php echo _l('add_to_cart');?>
			            </button>
	            	</td>
  				</tr>
    
               
            </table> 
        </div> 
  
<div> 

        <!-- buttons -->
         
        <div class="col-md-4"> 
        	<button 
                class="btn btn-block btn-info" 
                onclick="window.location.href='#/cart'">
                <i class="icon-chevron-left" /> <?php echo _l('proceed_to_cart');?>
            </button>
        	
            <button 
                class="btn btn-block" 
                onclick="window.location.href=''">
                <i class="icon-chevron-left" /> <?php echo _l('back_to_store');?>
            </button>
        </div>
    </div>

