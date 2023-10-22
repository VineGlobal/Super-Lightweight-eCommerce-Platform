<?php
include("../../main_oauth.php");
?>
 
 <div class="col-md-12 store-cart-content">
    <h3>
         <?php echo _l('customer_service');?> 
    </h3>
  </div>
 
<div class="clear"></div>
    <div class="col-md-12 content-backcolor">
        <div class="col-md-8">
			<div ng-controller="TabsCustomerService">
			  <tabset justified="true">
			    <tab heading="<?php echo _l('contact_us');?>">
			 
	<h2><?php echo _l('contact_us');?></h2>
            
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="well well-sm">
                <form id="contactForm" name="contactForm" onsubmit="submitForm()">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Name" required="required" data-error="Name is missing"/>
                        </div>
                        <div class="form-group">
                            <label for="email">
                                Email Address</label>
                            <div class="input-group">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span>
                                </span>
                                <input type="email" class="form-control" id="email" placeholder="Enter email" required="required" /></div>
                        </div>
                        <div class="form-group">
                            <label for="subject">
                                Subject</label>
                            <select id="subject" name="subject" class="form-control" required="required">
                                <option value="" selected>Choose One:</option>
                                <option value="service">General Customer Service</option>
                                <option value="suggestions">Suggestions</option>
                                <option value="product">Product Support</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                Message</label>
                            <textarea name="message" id="message" class="form-control" rows="9" cols="25" required="required"
                                placeholder="Message"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary pull-right" id="form-submit">
                            Send Message</button>
                    </div>
                    <br/>
                     <div id="msgSubmit" class="h3 text-center hidden"></div>
                </div>
                </form>
            </div>
        </div>
        <div class="col-md-3">
            <form>
            <legend><span class="glyphicon glyphicon-globe"></span> Our office</legend>
            <address>
                <strong><?php echo _m('company_name');?></strong><br>
                <?php echo _m('company_address_1');?><br>
                <?php echo _m('company_city');?>, <?php echo _m('company_state');?> <?php echo _m('company_zip_code');?><br>
                <abbr title="Phone">
                    P:</abbr>
                <?php echo _m('company_phone_number');?>
            </address>
            <address>
                <strong><?php echo _l('customer_service_email');?></strong><br>
                <a href="mailto:<?php echo _m('company_customer_service_email');?>"><?php echo _m('company_customer_service_email');?></a>
            </address>
            </form>
        </div>
    </div>
</div>
			    	
			    </tab>
			    <tab heading="<?php echo _l('return_policy');?>">
			    	<?php echo _l('return_policy_text');?>
			    </tab>
			    <tab heading="<?php echo _l('Shipping');?>"><?php echo _l('shipping_policy_text');?></tab>
			    <!-- <tab heading="<?php echo _l('International');?>">International Information Goes Here</tab> -->
			    <tab heading="<?php echo _l('faq');?>"><?php echo _l('faq_text');?></tab>
			  </tabset>
			</div>			 
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

