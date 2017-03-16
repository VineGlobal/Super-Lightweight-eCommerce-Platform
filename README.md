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

Screen Capture of the AngularJS E-commerce (Digital Commerce) Product Page
![AngularJS E-Commerce Product Page](http://blog.vineglobal.com/images/home-page.PNG)


Screen Capture of the AngularJS E-commerce (Digital Commerce) Shopping Cart
![AngularJS E-Commerce Shopping Cart](http://blog.vineglobal.com/images/shopping-cart.PNG)
