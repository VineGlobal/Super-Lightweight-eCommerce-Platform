<?php
include("../../main_oauth.php");
?>
  <div id="store" class="content col-md-12" style="overflow: hidden;" ng-controller="AdminController">  
    <form class="form-search"> 
                    <div class="col-md-12 store-cart-content">
                      <div class="content-store col-md-6" >
                        <a href="#/cart" title="go to shopping cart" ng-disabled="cart.getTotalCount() < 1">
                                        <img src="images/cart.png" alt="" />
                                        <b>{{cart.getTotalCount()}}</b> <?php echo _l('items');?>, <b>{{cart.getTotalPrice() | currency}}</b>
                                    </a>
                        </div>
                       <div class="tabs-menu col-md-6">
                         <ul class="cbp-vm-options">
                            <li ng-repeat="section in sections">
                                <a class="cbp-vm-icon cbp-vm-grid {{section.class}}" ng-class="{cbpvmselected : isSelected(section)}"  ng-click="setMaster(section)">{{section.name}}</a>
                            </li>
                        </ul>
                       </div>
                    </div>
                    <div class="col-md-12 store-button-top">
                        <div class="content-search widget_search">
                           <input type="text" ng-model="query" ng-change="search('name')" class="search-query" placeholder="<?php echo _l('search');?>">
                           <input type="submit" value="Go">
                        </div>
                        <div class="filter-button-top">
                        	<button class="btn flr-top-first active" ng-click="myFilter('category',null)"><?php echo _l('all');?></button>
                        	<?php foreach ($types['categories'] as $category_key => $category_value) { ?>
                           		<button class="btn flr-top" ng-click="myFilter('category','<?php echo $category_key;?>')"><?php echo $category_value;?></button>
                           <?php } ?>
                           
                       </div>
                    </div>
                    
                    <div id="cbp-vm" class="cbp-vm-switcher" ng-class="{true: '', false: 'activegrid'}[isActive]">
                        <ul id="product" class="nav-pills nav-stacked rectangle-list">
                            <li class="my-gallery-animation" ng-repeat="product in pagedItems[currentPage]" >
                                <div class="img-prod">
                                
                                <a class="details-button" href="#/products/{{product.code}}">
	                                   <img ng-src="{{product.src}}" alt="{{product.name}}" />
	                                </a>	
                                </div>

                				<div class="description">
                					<a class="title" href="#/products/{{product.code}}"> {{product.name}}  </a>
                				</div>	                
				
                                <div class="description">{{product.description}}</div> 
                    			
                    			<p class="content_desc"> 
                                        {{product.price | currency}} 
                                        <span ng-show="cart.getTotalCount(product.code) > 0"><i class="fa fa-check"></i>{{cart.getTotalCount(product.code)}}</span>
                                </p>
                                <!-- <p class="content_desc">
	                                <a class="details-button" href="" ng-click="cart.addItem(product.code, product.name, product.price, 1)">
	                                   <div class="btn btn-success"><?php echo _l('add_cart');?></div>
	                                </a>
                                </p>
                               -->
                                <div class="clear"></div>
                          </li> 
                      </ul>
                      <div class="col-md-12 store-cart-content store-cart-footer">
                       <div class="content-store col-xs-6 col-md-6" >
                        <a href="#/cart" title="go to shopping cart" ng-disabled="cart.getTotalCount() < 1">
                                        <img src="images/cart.png" alt="" />
                                        <b>{{cart.getTotalCount()}}</b> <?php echo _l('items');?>, <b>{{cart.getTotalPrice() | currency}}</b>
                                    </a>
                        </div>
                        <div class="col-xs-6 col-md-6 store-down-bottom">
                          <div class='bt-direction'>
                               <button class="btn store-pag-button" ng-disabled="currentPage == 0" ng-click="currentPage=currentPage-1">
                                    <?php echo _l('previous');?>
                                </button>
                                <p>{{currentPage + 1}}/{{numberOfPages}}</p>
                                <button class="btn btn-pag-next" ng-disabled="currentPage >= store.products.length/pageSize - 1" ng-click="currentPage=currentPage+1">
                                    <?php echo _l('next');?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
    </form>
  </div>

      <!-- Filter Images--><script type="text/javascript">
    $('.filter-button-top button').on('click', function () {
        $('.filter-button-top button').removeClass('active');
        $(this).addClass('active');
    });</script>