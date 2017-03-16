# Google Spreadsheets PHP API
PHP library allowing read/write access to existing Google Spreadsheets and their data. Uses the [version 3 API](https://developers.google.com/google-apps/spreadsheets/), which is the latest at time of writing.

Since this API uses [OAuth2](http://oauth.net/2/) for client authentication a *very lite* (and somewhat incomplete) set of [classes for obtaining OAuth2 tokens](oauth2) is included.

- [Requires](#requires)
- [Methods](#methods)
	- [API()](#api)
	- [API()->getSpreadsheetList()](#api-getspreadsheetlist)
	- [API()->getWorksheetList()](#api-getworksheetlist)
	- [API()->getWorksheetDataList()](#api-getworksheetdatalist)
	- [API()->getWorksheetCellList()](#api-getworksheetcelllist)
	- [API()->updateWorksheetCellList()](#api-updateworksheetcelllist)
- [Example](#example)
- [Issues](#issues)
- [Links](#links)

## Requires
- PHP 5.4 (uses [anonymous functions](http://php.net/manual/en/functions.anonymous.php) extensively).
- [cURL](https://php.net/curl).
- Expat [XML Parser](http://docs.php.net/manual/en/book.xml.php).

## Methods

### API()
Constructor accepts an instance of `OAuth2\GoogleAPI()`, which handles OAuth2 token fetching/refreshing and generation of HTTP authorization headers used with all Google spreadsheet API calls.

The included [`example.php`](example.php) provides [usage](example.php#L25-L36) [examples](base.php#L12-L22).

### API()->getSpreadsheetList()
Returns a listing of available spreadsheets for the requesting client.

```php
$OAuth2GoogleAPI = new OAuth2\GoogleAPI(/* URLs and client identifiers */);
$OAuth2GoogleAPI->setTokenData(/* Token data */);
$OAuth2GoogleAPI->setTokenRefreshHandler(/* Token refresh handler function */);
$spreadsheetAPI = new GoogleSpreadsheet\API($OAuth2GoogleAPI);

print_r(
	$spreadsheetAPI->getSpreadsheetList()
);

/*
[SPREADSHEET_KEY] => Array
(
	[ID] => 'https://spreadsheets.google.com/feeds/spreadsheets/private/full/...'
	[updated] => UNIX_TIMESTAMP
	[name] => 'Spreadsheet name'
)
*/
```

[API reference](https://developers.google.com/google-apps/spreadsheets/#retrieving_a_list_of_spreadsheets)

### API()->getWorksheetList()
Returns a listing of defined worksheets for a specified spreadsheet key.

```php
$OAuth2GoogleAPI = new OAuth2\GoogleAPI(/* URLs and client identifiers */);
$OAuth2GoogleAPI->setTokenData(/* Token data */);
$OAuth2GoogleAPI->setTokenRefreshHandler(/* Token refresh handler function */);
$spreadsheetAPI = new GoogleSpreadsheet\API($OAuth2GoogleAPI);

print_r(
	$spreadsheetAPI->getWorksheetList('SPREADSHEET_KEY')
);

/*
[WORKSHEET_ID] => Array
(
	[ID] => 'https://spreadsheets.google.com/feeds/...'
	[updated] => UNIX_TIMESTAMP
	[name] => 'Worksheet name'
	[columnCount] => TOTAL_COLUMNS
	[rowCount] => TOTAL_ROWS
)
*/
```

[API reference](https://developers.google.com/google-apps/spreadsheets/#retrieving_information_about_worksheets)

### API()->getWorksheetDataList()
Returns a read only 'list based feed' of data for a given spreadsheet key and worksheet ID.

List based feeds have a specific format as defined by Google - see the [API reference](https://developers.google.com/google-apps/spreadsheets/#retrieving_a_list-based_feed) for details. Data is returned as an array with two keys - defined headers and the data body.

```php
$OAuth2GoogleAPI = new OAuth2\GoogleAPI(/* URLs and client identifiers */);
$OAuth2GoogleAPI->setTokenData(/* Token data */);
$OAuth2GoogleAPI->setTokenRefreshHandler(/* Token refresh handler function */);
$spreadsheetAPI = new GoogleSpreadsheet\API($OAuth2GoogleAPI);

print_r(
	$spreadsheetAPI->getWorksheetDataList('SPREADSHEET_KEY','WORKSHEET_ID')
);

/*
Array
(
	[headerList] => Array
	(
		[0] => 'Header name #1'
		[1] => 'Header name #2'
		[x] => 'Header name #x'
	)

	[dataList] => Array
	(
		[0] => Array
		(
			['Header name #1'] => VALUE
			['Header name #2'] => VALUE
			['Header name #x'] => VALUE
		)

		[1]...
	)
)
*/
```

[API reference](https://developers.google.com/google-apps/spreadsheets/#retrieving_a_list-based_feed)

### API()->getWorksheetCellList()
Returns a listing of individual worksheet cells, for either the entire sheet or a specific row/column range - see example below for usage of row/column ranges.

Cells are returned as instances of [`GoogleSpreadsheet\CellItem()`](googlespreadsheet/cellitem.php) within an array list, indexed by their cell reference (e.g. "B1"). Cell instances can be modified and then passed into [`API()->updateWorksheetCellList()`](#api-updateworksheetcelllist) to update the source Google spreadsheet.

```php
$OAuth2GoogleAPI = new OAuth2\GoogleAPI(/* URLs and client identifiers */);
$OAuth2GoogleAPI->setTokenData(/* Token data */);
$OAuth2GoogleAPI->setTokenRefreshHandler(/* Token refresh handler function */);
$spreadsheetAPI = new GoogleSpreadsheet\API($OAuth2GoogleAPI);

// fetch first 20 rows from third column (C) to the end of the sheet
// if no $cellRange is passed, all cells for a spreadsheet will be returned
$cellRange = [
	'columnStart' => 3
	'rowStart' => 1
	'rowEnd' => 20
];

print_r(
	$spreadsheetAPI->getWorksheetCellList(
		'SPREADSHEET_KEY','WORKSHEET_ID',
		$cellRange
	)
);

/*
Array
(
	[CELL_REFERENCE] => GoogleSpreadsheet\CellItem Object
	(
		getRow()
		getColumn()
		getReference()
		getValue()
		setValue()
		isDirty()
	)

	[CELL_REFERENCE]...
)
*/
```

[API reference](https://developers.google.com/google-apps/spreadsheets/#retrieving_a_cell-based_feed)

### API()->updateWorksheetCellList()
Accepts and array list of one or more `GoogleSpreadsheet\CellItem()` instances and updates the target spreadsheet where cell values have been modified from their source value using the [`GoogleSpreadsheet\CellItem()->setValue()`](googlespreadsheet/cellitem.php#L62-L65) method.

Passed cell instances that have not been modified will be skipped by this method (no work to do).

```php
$OAuth2GoogleAPI = new OAuth2\GoogleAPI(/* URLs and client identifiers */);
$OAuth2GoogleAPI->setTokenData(/* Token data */);
$OAuth2GoogleAPI->setTokenRefreshHandler(/* Token refresh handler function */);
$spreadsheetAPI = new GoogleSpreadsheet\API($OAuth2GoogleAPI);

$cellList = $spreadsheetAPI->getWorksheetCellList('SPREADSHEET_KEY','WORKSHEET_ID');
$cellList['CELL_REFERENCE']->setValue('My updated value');
$spreadsheetAPI->updateWorksheetCellList(
	'SPREADSHEET_KEY','WORKSHEET_ID',
	$cellList
);
```

[API reference](https://developers.google.com/google-apps/spreadsheets/#updating_multiple_cells_with_a_batch_request)

## Example
The provided [example](example.php) CLI script will perform the following tasks:
- Fetch all available spreadsheets for the requesting client and display.
- For the first spreadsheet found, fetch all worksheets and display.
- Fetch a data listing of the first worksheet.
- Fetch a range of cells for the first worksheet.
- Finally, modify the content of the first cell fetched (commented out in example).

### Setup
- Create a new project API at https://console.developers.google.com/.
	- Generate a new set of OAuth2 client tokens under the **APIs & Auth -> Credentials** section:
		- Click **Create new Client ID**.
		- Select **Web application** as the **Application type** (default).
		- Enter an **Authorized redirect URI** - this *does not* need to be a real live URI for the example.
		- Under the **Client ID for web application** section, note down generated **client ID** and **client secret** values.
- Modify [`config.php`](config.php) entering `redirect`, `clientID` and `clientSecret` as generated above.
- Execute [`buildrequesturl.php`](buildrequesturl.php) and enter generated URL into a new browser window.
- After accepting terms you will be taken back to the entered redirect URI along with a `?code=` querystring value.
- Execute [`exchangecodefortokens.php`](exchangecodefortokens.php), providing `code` from the previous step.
- Received OAuth2 token credentials will be saved to `./.tokendata`.
	- **Note:** In a production application this sensitive information should be saved in a secure form to datastore/database/etc.

Finally, run `example.php` to view the result.

**Note:** If OAuth2 token details stored in `./.tokendata` require a refresh (due to expiry), the function handler set by [`OAuth2\GoogleAPI->setTokenRefreshHandler()`](oauth2/googleapi.php#L36-L39) will be called to allow the re-save of updated token data back to persistent storage.

## Issues
The Google spreadsheet API documents suggest requests can [specify the API version](https://developers.google.com/google-apps/spreadsheets/#specifying_a_version). Attempts to do this cause the [cell based feed](https://developers.google.com/google-apps/spreadsheets/#retrieving_a_cell-based_feed) response to avoid providing the cell version slug in `<link rel="edit">` nodes - making it impossible to issue an update of cell values. So for now, I have left out sending the API version HTTP header.

## Links
- OAuth2
	- http://tools.ietf.org/html/rfc6749
	- https://developers.google.com/accounts/docs/OAuth2WebServer
	- https://developers.google.com/oauthplayground/
- Google Spreadsheets API version 3.0
	- https://developers.google.com/google-apps/spreadsheets/
