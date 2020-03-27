# AngularJS E-commerce (Digital Commerce) Platform
Super Lightweight Digital Commerce Platform -- Built with AngularJS (Storefront) and Google Sheets (Back-end)

This e-commerce atform is unique in several ways. 

First, the e-commerce platform;s data is stored entirely in Google Sheets.
The product catalog, category management, and multi-lingual labels are all maintained in Google Sheets.

Secondly, performance is very fast and data modifications are real-time. Meaning, if you change any of the data in Google Sheets, the data is published to the storefront's data cache in real-time.

To improve performance, the storefront's data is cached locally. 

A Google Sheet script (macro) is triggered, and sends the data to the storefront via webhook. This webhook receives the data and automatically clears the data. 

Other notable features:

- Mobile responsive. Looks great on both desktops and mobile devices. Give it a try (demo): https://landedcost.io/demo
- Supports PayPal Express. Other payment options to follow soon.
- Built with AngularJS by Google on the front-end.
- Very theme-able: The sample site theme can be easily modified.
- It's FREE.
- Mult-lingual support; add as many languages as you want. 
 

## Getting Started

### Installation

**For this installation, we are giving you access to use our Google API's project OAuth 2.0 client IDs -- please, please be kind). This will make the initial setup much easier for first users of Google API Managers.**

1. On the AngularJS-eCommerce Github repository page, click Clone or download button. These instructions will cover the download option.
2. Download and unzip the ZIP file to your Web server.
3. Open your browser, and the webstore should be running.

The Googgle Sheet that contains the data (All changes made will go live):
https://docs.google.com/spreadsheets/d/12Ljjd8dChaHq2YOExKXw77ri2UDgixQTVuZBgMY-o30/edit#gid=0

