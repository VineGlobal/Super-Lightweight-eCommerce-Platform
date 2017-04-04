# AngularJS E-commerce (Digital Commerce) Platform
Super Lightweight Digital Commerce Platform -- Built with AngularJS and Google Sheets   

This platform is unique in several ways. 

First, the e-commerce storefront's data is stored completely in Google Sheets.
The product catalog, category management, and multi-lingual labels (strings) are maintained in Google Sheets.

![Google Sheets e-commerce database](http://blog.vineglobal.com/images/google-sheets-live.png)

Secondly, performance is very fast and data updates are real-time.
To improve performance, the storefront's data is cached locally. However, when the data is updated
on the Google Sheet side, a Google Sheet script (macro) is triggered, and sends the data to your Web server via webhook. This webhook
receives the data and automatically clears the data. 


Other notable features:

- Mobile responsive. Looks great on both desktops and mobile devices. Give it a try: http://store.vineglobal.com
- Supports PayPal Express. Other payment options to follow soon.
- Built with AngularJS by Google on the front-end.
- Very theme-able: The sample site theme can be easily modified.
- It's FREE.
- Mult-lingual support; add as many languages as you want.

**Screen Capture of the AngularJS E-commerce (Digital Commerce) Product Page**

<img src="http://blog.vineglobal.com/images/home-page.PNG" alt="AngularJS E-Commerce Product Page" width="50%"/>


**Screen Capture of the AngularJS E-commerce (Digital Commerce) Shopping Cart**

<img src="http://blog.vineglobal.com/images/shopping-cart.PNG" alt="AngularJS E-Commerce Shopping Cart" width="50%"/>

## Getting Started

### Installation

1. On the AngularJS-eCommerce Github repository page, click Clone or download button. These instructions will cover the download option.
2. Download and unzip the ZIP file to your Web server.
3. If you try to run the application, you will receive the following error message:
4. Log into the Google API Console: https://console.developers.google.com If you haven't created a Google Developers account, you will be required to create an account.
5. Once you are logged in, click on the Credentials link. <img src="http://blog.vineglobal.com/images/credentials.png" alt="AngularJS E-Commerce Shopping Cart" width="50%"/>
6. 
