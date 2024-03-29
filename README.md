# LandedCost.io Digital Commerce Platform
Super Lightweight Digital Commerce Platform -- Built with AngularJS (Storefront) and Google Sheets (Back-end) and integrated with a landed cost calculator to support cross-border e-commerce transactions

This e-commerce atform is unique in several ways. 

First, the e-commerce platform's backend data is managed 100% with Google Sheets.
The product catalog, categories, and multi-lingual labels are all maintained in Google Sheets. 
Content and product catalog modifications are published in real-time to the storefront. 

Secondly, storefront performance is very fast. 

Other notable features:

- Mobile responsive. Looks great on both desktops and mobile devices. Give it a try (demo): https://landedcost.io/demo
- Integrated with the LandedCost.io landed cost API, which calculates import duties and taxes for cross-border ecommerce orders.
  First and only digital commerce platform with native landed cost integration!
- Supports PayPal Express. Other payment options to follow soon.
- The client-side framework is AngularJS.
- Very theme-able: The sample site theme can be easily modified.
- It's FREE.
- Mult-lingual support; add as many languages as you want. 
 
### Installation

**For this live tutorial, we are giving you access to use our Google API's project OAuth 2.0 client IDs -- please, please be kind). This will make the initial setup much easier for first time users of the Google Sheet API.**

1. On the Super-Lightweight-eCommerce-Platform Github repository page, click Clone or download button. These instructions will cover the download option.
2. Download and unzip the ZIP file to your Web server.
3. Make the cached-files folder writable.
4. The storefront should be accessible in your browser
5. The following Googgle Sheet provides the data for the storefront. Upcon accessing the Google Sheet, you will need to request editor access. We normally respond to each request less than 4-8 hours. 
https://docs.google.com/spreadsheets/d/12Ljjd8dChaHq2YOExKXw77ri2UDgixQTVuZBgMY-o30/edit#gid=0
6. If you wish to modify the Google Sheets data and see the data changes in your storefront, please update the Webhook URL setting in the store_config tab. The Webhook URL should be your webhook's public URL location.

![image](https://github.com/VineGlobal/Super-Lightweight-eCommerce-Platform/assets/817291/d8e1fc89-e2b0-4abe-bb10-1ae2ca053204)

7. To publish the Google Sheets data updates to your storefront click on the Publish Store Data menu item.

![image](https://github.com/VineGlobal/Super-Lightweight-eCommerce-Platform/assets/817291/b736df73-5c09-426b-9443-5e88a1ed625f)

8. If you find this platform useful, and plan to use it long-term, you will need to create a copy of the Google Sheets and request your P12 Key from Google.

If you have any questions or feedback, please email us at admin@landedcost.io

Thank You,
LandedCost.io Team



