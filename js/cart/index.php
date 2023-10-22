<?php header('Content-Type: application/javascript');?>
<?php
include("../../main_oauth.php");

?>

//----------------------------------------------------------------
// shopping cart
//
function shoppingCart(cartName) {
    this.cartName = cartName;
    this.clearCart = false;
    this.checkoutParameters = {};
    this.items = [];
    this.shipFromCountry = "";
    this.shipToCountry = "";       
  

    // load items from local storage when initializing
    this.loadItems();      
    
    this.loadDropdowns();   
    
     

    // save items to local storage when unloading
    var self = this;
    $(window).unload(function () {
        if (self.clearCart) {
            self.clearItems();
        }
        self.saveItems();
        self.clearCart = false;
        
        self.saveDropdowns();
    });
}


// load items from local storage
shoppingCart.prototype.loadDropdowns = function () {
    var dropdowns = localStorage != null ? localStorage[this.cartName + "_dropdowns"] : null;
    if (dropdowns != null && JSON != null) {
        try {
            var jsonDropdowns       = JSON.parse(dropdowns);
            this.shipToCountry      = jsonDropdowns.shipToCountry;
            this.shipFromCountry    = jsonDropdowns.shipFromCountry;
        }
        catch (err) {
            //set the defaults
           //  this.shipToCountry      = 'CA-AB';
           // this.shipFromCountry    = 'US';
        }
    }
}


// save items to local storage
shoppingCart.prototype.saveDropdowns = function () {
    if (localStorage != null && JSON != null) {
        var obj = { shipFromCountry: this.shipFromCountry, shipToCountry: this.shipToCountry };
        localStorage[this.cartName + "_dropdowns"] = JSON.stringify(obj);
    }
}

// load items from local storage
shoppingCart.prototype.loadItems = function () {
    var items = localStorage != null ? localStorage[this.cartName + "_items"] : null;
    if (items != null && JSON != null) {
        try {
            var items = JSON.parse(items);
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                if (item.code != null && item.name != null && item.price != null && item.quantity != null) {
                    item = new cartItem(item.code, item.name, item.price, item.quantity,item.description);
                    this.items.push(item);
                }
            }
        }
        catch (err) {
            // ignore errors while loading...
        }
    }
}

// save items to local storage
shoppingCart.prototype.saveItems = function () {
    if (localStorage != null && JSON != null) {
        localStorage[this.cartName + "_items"] = JSON.stringify(this.items);
    }
}

// save items to local storage
shoppingCart.prototype.calculateLandedCost = function () {
    
    var st                  = document.getElementById("shipToCountry");
    var shipToCountryCode   = st.options[st.selectedIndex].value;
    
    var sf                  = document.getElementById("shipFromCountry");
    var shipFromCountryCode = sf.options[sf.selectedIndex].value;
    
    var cartItems          = []; 
     
    var subTotal           = 0;
    
     for (var i = 0; i < this.items.length; i++) {
        var item            = this.items[i];
       
        var cartItem = {   
            sku                 : item.code,
            description         : item.description,
            name                : item.name,
            quantity            : item.quantity,
            price               : item.price.toFixed(2),
            weight              : 1,
            countryOfOrigin     : "CN" ,
            autoClassify        : "AUTO-CLASSIFY",
            uom                 : "lbs", 
            category            : "open",
            hsCode              : ""
        };    
        
        cartItems.push(cartItem);
        
        subTotal += (Number(item.quantity)* Number(item.price.toFixed(2)));
               
    }        
    
     subTotal = Number(subTotal) +  Number(this.getTotalShipping());
     
     var postData = {
          shipToCountryCode      : shipToCountryCode,
          shipFromCountryCode    : shipFromCountryCode,
          shippingTotal          : this.getTotalShipping(),
          cartItems              : cartItems
         };
                               
         $.post( "landedcost/",{cart:postData}, function(data) {      
           
            // shoppingCart.prototype.grandTotal            = data.grandTotal;
            ///  shoppingCart.prototype.dutiesTotal         = data.dutiesTotal;
            /// shoppingCart.prototype.taxesTotal           = data.taxesTotal;  
            
            if (data.dutiesTotal !== undefined) {
                $('#taxesTotal').html(formatCurrency(data.taxesTotal));
                $('#dutiesTotal').html(formatCurrency(data.dutiesTotal));
                $('#grandTotal').html(formatCurrency(data.grandTotal)); 
            } else {
                
                $('#taxesTotal').html(formatCurrency("0.00"));
                $('#dutiesTotal').html(formatCurrency("0.00"));
                $('#grandTotal').html(formatCurrency(subTotal));
            }
             $('#click_calculate_reminder').hide();            
                           
              
        }) ;           
}



// adds an item to the cart
shoppingCart.prototype.addItem = function (code, name, price, quantity,description) {
    quantity = this.toNumber(quantity);
    if (quantity != 0) {

        // update quantity for existing item
        var found = false;
        for (var i = 0; i < this.items.length && !found; i++) {
            var item = this.items[i];
            if (item.code == code) {
                found = true;
                item.quantity = this.toNumber(item.quantity + quantity);
                
                /*remove on the quantity updates **/
                shoppingCart.prototype.dutiesTotal = null;
                shoppingCart.prototype.taxesTotal  = null;
                shoppingCart.prototype.grandTotal = null;    
                $('#dutiesTotal').html('');
                $('#taxesTotal').html('');
                $('#grandTotal').html(''); 
                $('#click_calculate_reminder').show();
               
                
                if (item.quantity <= 0) {
                    this.items.splice(i, 1);
                }
            }
        }

        // new item, add now
        if (!found) {
            var item = new cartItem(code, name, price, quantity,description);
            this.items.push(item);
              shoppingCart.prototype.dutiesTotal = null;
              shoppingCart.prototype.taxesTotal  = null;
              shoppingCart.prototype.grandTotal = null;   
               $('#dutiesTotal').html('');
                $('#taxesTotal').html('');
                $('#grandTotal').html(''); 
                $('#click_calculate_reminder').show();    
            
        }

        // save changes
        this.saveItems();
        
        /** no items in the cart, remove the totals **/
        if (this.items.length == 0) {
            this.removeTotals();   
        }
        
    }
}

// get the total price for all items currently in the cart
shoppingCart.prototype.getTotalPrice = function (code) {
    var total = 0;
    for (var i = 0; i < this.items.length; i++) {
        var item = this.items[i];
        if (code == null || item.code == code) {
            total += this.toNumber(item.quantity * item.price);
        }
    }
    return total;
}

// get the total price for all items currently in the cart
shoppingCart.prototype.getTotalTax = function () {     
    return shoppingCart.prototype.taxesTotal;       
}

// get the total price for all items currently in the cart
shoppingCart.prototype.getTotalDuties = function () {    
    return shoppingCart.prototype.dutiesTotal;
}

// get the total price for all items currently in the cart
shoppingCart.prototype.getTotalShipping  = function () {
   var total = 0;                          
    total = ((<?php echo _m('shipping_rate');?> * Math.round((this.getTotalPrice() * 100) / 100)) / 100);  
    return total;
}


 

// get the total price for all items currently in the cart
shoppingCart.prototype.getTotalCount = function (code) {
    var count = 0;
    for (var i = 0; i < this.items.length; i++) {
        var item = this.items[i];
        if (code == null || item.code == code) {
            count += this.toNumber(item.quantity);
        }
    }
    return count;
}

// clear the cart
shoppingCart.prototype.clearItems = function () {
    this.items = [];
    this.saveItems();     
   shoppingCart.prototype.dutiesTotal = null;
   shoppingCart.prototype.taxesTotal  = null;
   shoppingCart.prototype.grandTotal = null;   
   $('#dutiesTotal').html('');
   $('#taxesTotal').html('');
   $('#grandTotal').html('');      
   $('#click_calculate_reminder').show();
 

    
    this.removeTotals();           
    
}



   shoppingCart.prototype.removeTotals = function () {
    /** remove the last four table rows (totals) */
    document.getElementById("cartTable").deleteRow(document.getElementById("cartTable").rows.length-1);
    document.getElementById("cartTable").deleteRow(document.getElementById("cartTable").rows.length-1);
    document.getElementById("cartTable").deleteRow(document.getElementById("cartTable").rows.length-1);
    document.getElementById("cartTable").deleteRow(document.getElementById("cartTable").rows.length-1);
    }

// define checkout parameters
shoppingCart.prototype.addCheckoutParameters = function (serviceName, merchantID, options) {

    // check parameters
    if (serviceName != "PayPal") {
        throw "serviceName must be 'PayPal'.";
    }
    if (merchantID == null) {
        throw "A merchantID is required in order to checkout.";
    }

    // save parameters
    this.checkoutParameters[serviceName] = new checkoutParameters(serviceName, merchantID, options);
}
// check out
shoppingCart.prototype.checkout = function (serviceName, clearCart) {

    // select serviceName if we have to
    if (serviceName == null) {
        var p = this.checkoutParameters[Object.keys(this.checkoutParameters)[0]];
        serviceName = p.serviceName;
    }

    // sanity
    if (serviceName == null) {
        throw "Use the 'addCheckoutParameters' method to define at least one checkout service.";
    }

    // go to work
    var parms = this.checkoutParameters[serviceName];
    if (parms == null) {
        throw "Cannot get checkout parameters for '" + serviceName + "'.";
    }
    switch (parms.serviceName) {
        case "PayPal":
            this.checkoutPayPal(parms, clearCart);
            break;
        default:
            throw "Unknown checkout service: " + parms.serviceName;
    }
}

// check out with PayPal
shoppingCart.prototype.checkoutPayPal = function (parms, clearCart) {

    // global data
    var data = {
        cmd: "_cart",
        business: parms.merchantID,
        upload: "1",
        rm: "2",
        charset: "utf-8"
    };

    // item data
    for (var i = 0; i < this.items.length; i++) {
        var item = this.items[i];
        var ctr = i + 1;
        data["item_number_" + ctr]  = item.code;
        data["item_name_" + ctr]    = item.name;
        data["quantity_" + ctr]     = item.quantity;
        data["amount_" + ctr]       = item.price.toFixed(2);
        data["currency_code"]       = "USD";
        data["shipping_1"]          = this.getTotalShipping(); 
        data["tax_cart"]            = this.getTotalTax() ;
    }
    
    
 	data["cancel_return"] 		= "https://<?php echo $_SERVER['SERVER_NAME'];?>";
 	data["return"] 				= "https://<?php echo $_SERVER['SERVER_NAME'];?>/notify_url";
 	data["notify_url"] 			= "https://<?php echo $_SERVER['SERVER_NAME'];?>/notify_url";

    // build form
    var form = $('<form/></form>');
    //    form.attr("action", "https://www.paypal.com/cgi-bin/webscr");
    form.attr("action", "https://www.sandbox.paypal.com/us/cgi-bin/webscr");
    form.attr("method", "POST");
    form.attr("style", "display:none;");
    this.addFormFields(form, data);
    this.addFormFields(form, parms.options);
    $("body").append(form);

    // submit form
    this.clearCart = clearCart == null || clearCart;
    form.submit();
    form.remove();
}


// utility methods
shoppingCart.prototype.addFormFields = function (form, data) {
    if (data != null) {
        $.each(data, function (name, value) {
            if (value != null) {
                var input = $("<input></input>").attr("type", "hidden").attr("name", name).val(value);
                form.append(input);
            }
        });
    }
}
shoppingCart.prototype.toNumber = function (value) {
    value = value * 1;
    return isNaN(value) ? 0 : value;
}

//----------------------------------------------------------------
// checkout parameters (payment service)
//
function checkoutParameters(serviceName, merchantID, options) {
    this.serviceName = serviceName;
    this.merchantID = merchantID;
    this.options = options;
}

//----------------------------------------------------------------
// items in the cart
//
function cartItem(code, name, price, quantity,description) {
    this.code           = code;
    this.name           = name;
    this.price          = price * 1;
    this.quantity       = quantity * 1;
    this.description    = description;
}


function NumberFormatter(locale, opts) {
    var formatNumber,
        defaults = {   
            currency: 'USD',
            minimumFractionDigits: 2
        };
    opts = opts || {};
    opts = Object.assign({}, defaults, opts);
    
    formatNumber = new Intl.NumberFormat(locale, opts);
    return formatNumber.format;
};

function formatCurrency(amount) { 
     var formatter = new NumberFormatter('en-US');
     return(formatter(amount));
       
}


               
