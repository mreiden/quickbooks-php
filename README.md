# QuickBooks PHP DevKit

QuickBooks integration support for PHP 7.2+

## *\*\*\* Note about this Repo:*

This is a fork of consolibyte/quickbooks-php until it is (hopefully) merged into that repo.


## Installation

The preferred method of installation is via [Composer][]. Run the following command to install the package and add it as a requirement to your project's `composer.json`:

```bash
composer config repositories.QuickBooksPhpDevKit vcs https://github.com/mreiden/quickbooks-php
composer require consolibyte/quickbooks:dev-master
```


## *\*\*\* Objectives of this fork are:*

- Regenerate QBXML\Schema\Objects using the QBXML version 13 Schema.  QBXML\Schema\Generator now generates the schema objects with correct maximum field lengths, locale restrictions, and minium version support.  Whether the path isOptional is still not supported.
- Use Namespaces
- Migrate global constants (QUICKBOOKS_\*) to class constants
- Support PHP 7.2+ which includes Sodium support by default.  In addition, 7.1 stops receiving security updates on December 1st, 2019.
- Use strict typing and type hinting
- Use json_encode/json_decode instead of PHP serialize/unserialize functions.  **You should set `PackageInfo::$ALLOW_PHP_UNSERIALIZE_EXTRA_DATA = true;` to allow existing queue items to process.**
- **Change default password hashing in Driver\Sql::\_authLogin to use PHP's password_hash function and its PASSWORD_DEFAULT algorithm.**
    + Setting `PackageInfo::$PASSWORD_ALLOW_LEGACY = true;` allows existing users to authenticate.  This can and should be removed once all user passwords have been upgraded.  Allowing legacy passwords will try the following in addition to the password_verify function:
      - Plain Text Password - Existing Check
      - md5(password) - Existing Check
      - sha1(password) - Existing Check
      - md5 and sha1 of salted password ($password.$salt).  Try with the default values of QUICKBOOKS_SALT and QUICKBOOKS_DRIVER_SQL_SALT
      - A user defined hash and/or salt.  **You need to set these using `\QuickBooksPhpDevKit\Driver\Sql::$PASSWORD_HASH = 'sha1';` and/or `\QuickBooksPhpDevKit\Driver\Sql::$PASSWORD_SALT = 'MyCustomSalt';`**
    + Setting `PackageInfo::$PASSWORD_UPGRADE = true;` will upgrade existing passwords when an existing user logs in.  This disabled by default so it doesn't break code that depends on the old password hashing, but should be enabled when possible.
- Add unit testing

## IMPORTANT NOTES !!
- Nothing has been done with Payments. **Do Not Use This Package If You Use Payments**
- Requires ext-dom (libxml2).  Pretty sure people have this since the code already uses SimpleXML in other places.
- You must replace `the QUICKBOOKS_\*` constants you use.  Finding and replacing `QUICKBOOKS\_((?:OBJECT|ADD|MOD|QUERY)\_[A-Z_]+)` with `PackageInfo::Actions['\1']` should handle the majority of these.
- The length of the quickbooks_config.module column must be increased because full class names are used and now include the namespace.
  * MySQL: `ALTER TABLE quickbooks_config MODIFY module VARCHAR(255) NOT NULL;`
  * PostgreSQL: `ALTER TABLE quickbooks_config ALTER COLUMN module TYPE varchar(255);`
  * SQLite3: Not sure.  Might need to rename the table, create a new one, and drop the old one.  It doesn't cause errors as SQLite3 seems to just truncate the string at the old 40 character limit.

## Additional Changes:
### QBXML:
- Allow removing QBXML value by ->set('field', null)
- AbstractQbxmlObject:
  * ->fromXML() used ucfirst(strtolower($type)) as the QBXML\Object\* class name, but the file (and class) names uppercase each word (e.g. ReceivePayment instead of Receivepayment).  This caused ::\_fromXMLHelper() to fail.
  * ->setFullNameType: Try to set the FullName, Name, and ParentRef FullName consistently.
  * ->set/getBooleanType: Use filter_var FILTER_VALIDATE_BOOLEAN to allow true, 'true', 'on', 'yes', 1, '1', etc instead of testing a subset of those directly.
  * ->set/getDateTYpe: Return null instead of 1969-12-31 if getDateType is called on an unset (null) value.  Add comments.
- Account:
  * Fix Query by name missing FullName (found via unit test)
  * Throw exception on setting an invalid AccountType
  * Remove get/set Balance, TotalBalance, SpecialAccountType, and CashFlowClassification as these are in the Response object and not the Request object.
- Bill:
  * Add get/setTxnID: required for modify transaction
  * Change asList method so line items are included in modify transaction
- Bill\ExpenseLine:
  * Use get/setAmountType for Amount
  * Throw exception on setting an invalid BillableStatus on an ExpenseLine
  * Add get/setTxnID: required for modify transaction
  * Add asXML method to include line items on Bill
- CreditMemo:
  * Fix asXML method to include CreditMemoLineMod
  * Add get/setTxnID: required for modify transaction
  * Add get/setPONumber, get/setDueDate, get/setCustomerSalesTaxCodeListID, get/setCustomerSalesTaxCodeFullName, get/setIsToBePrinted, get/setIsToBeEmailed, get/setIsPending, get/setShipAddress, and get/setBillAddress.
- CreditMemo\CreditMemoLine:
  * Add get/setTxnLineID: required for modify transaction
- Invoice:
  * Add code to get/setCustomerSalesTaxCodeListID and get/setCustomerSalesTaxCodeName so they are not empty methods.
- Invoice\InvoiceLine:
  * Add get/setTxnLineID: Required for modify transaction to modify or add invoice line items.
- Item:
  * Remove get/setFirstName and get/setLastName methods copied from Customer QBXML object.
- ServiceItem:
  * Add "Mod" to the end of SalesOrPurchase or SalesAndPurchase for Modify actions.

### Miscellaneous
- **Removed Utilities::constructRequestID and Utilities::parseRequestID:** There are no callers in the project since switching from `base64("$action|$ident")` to using the QUEUE table's primary key (quickbooks_queue_id) back in 2011.
- Utilities:checkRemoteAddress: Remove calls to `ereg` and replace with FILTER_VALIDATE_IP.  Fixes PHP 7+ when using `allow_remote_addr` or `deny_remote_addr` in WebConnector's handler_options.
- Utilities::intervalToSeconds: Simplify by using strtotime to figure out time interval strings (e.g. '15 minutes')
- Utilities::actionToObject: Check by array key instead of looping through every object.  Handle QBXML\Object\Class being called Qbclass.
- Use ramsey/uuid to generate ticket ids and GUIDs

### WebConnector:
- Server now checks that the handlers in the action->handlers map are able to be called.  This identifies missing methods/functions.
- Queue::enqueue and ::recurring use `Utilities::priorityForAction($action)` as the default priority instead of 0.




### Drivers:
- Driver\Sql::\_queueProcessing did not include the username in the query to get the latest record dequeued by the specified user.
- Fix Driver\Sql::\_authDisable and Driver\Sql::\_authEnable.  Missing $errnum, $errmsg to query function caused these to fail.  (found via unit test, but someone else fixed this in their repo years ago)
- Add Driver::authExists and Driver\Sql::_authExists methods.
- Remove protected method _mapSalt from Driver\Sql, Driver\Sql\Mysqli, Driver\Sql\Pgsql, and Driver\Sql\Sqlite3 since it is not called in the project.
- Driver\Sql::\_queueDequeue: Add an explicit `ORDER BY quickbooks_queue_id ASC` if $by_priority is false to return the next item in the order it was added to the QUEUE table.
- Driver called the wrong hook in several functions.  QUICKBOOKS_DRIVER_HOOK_AUTHRESOLVE was used instead of HOOK_QUEUEENQUEUE, HOOK_QUEUEDEQUEUE, HOOK_RECURENQUEUE, HOOK_RECURDEQUEUE, HOOK_QUEUEACTIONLAST, HOOK_QUEUEACTIONIDENTLAST, HOOK_ERRORLOG, HOOK_ERRORLAST
- Driver has property \_loglevel.  Use this in Sql and database driver subclasses instead of \_log_level.  Check \_loglevel consistently in all driver classes.  Some database drivers were checking and setting \_log_level while Driver and Driver\Sql checked \_loglevel.
- Allow DSN to be an associative array of ['backend', 'database', 'host', 'port', 'username', 'password'].  This bypasses having to urlencode these settings to use in a DSN string.
- Mssql: Removed driver since the mssql PHP extension was removed as of PHP 7.0.0.
- Mysql (not Mysqli): Removed driver since Mysql PHP extension was removed as of PHP 7.0.0.  If MySQL is chosen, it will use MySQLi instead.
- SQLite (not SQLite3): Removed driver since the SQLite extension was moved to PECL in PHP 5.4.  If SQLite is chosen, it will use SQLite3 instead.
- SQLite3:
  * Change table creation to include indexes and unique value constraints
- PostgreSQL:
  * Use lowercase table and column names
  * Use true/false for boolean instead of 1/0
  * `CREATE SCHEMA IF NOT EXISTS` if a schema other than "public" is specified in the database name (i.e. "databaseName.schemaName").  If the schema did not exist when initialize ran the tables ended up in the default "public" schema.
  * Fix Date/Timestamp column types.  A missing `break;` caused these to be TEXT columns.  This can be fixed by running these commands:
    ```ALTER TABLE quickbooks_config ALTER write_datetime TYPE timestamp without time zone USING write_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_config ALTER mod_datetime TYPE timestamp without time zone USING mod_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_log ALTER log_datetime TYPE timestamp without time zone USING log_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_oauthv1 ALTER request_datetime TYPE timestamp without time zone USING request_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv1 ALTER access_datetime TYPE timestamp without time zone USING access_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv1 ALTER touch_datetime TYPE timestamp without time zone USING touch_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_oauthv2 ALTER oauth_access_expiry TYPE timestamp without time zone USING oauth_access_expiry::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv2 ALTER oauth_refresh_expiry TYPE timestamp without time zone USING oauth_refresh_expiry::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv2 ALTER request_datetime TYPE timestamp without time zone USING request_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv2 ALTER access_datetime TYPE timestamp without time zone USING access_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv2 ALTER last_access_datetime TYPE timestamp without time zone USING last_access_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv2 ALTER last_refresh_datetime TYPE timestamp without time zone USING last_refresh_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_oauthv2 ALTER touch_datetime TYPE timestamp without time zone USING touch_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_queue ALTER enqueue_datetime TYPE timestamp without time zone USING enqueue_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_queue ALTER dequeue_datetime TYPE timestamp without time zone USING dequeue_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_recur ALTER enqueue_datetime TYPE timestamp without time zone USING enqueue_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_ticket ALTER write_datetime TYPE timestamp without time zone USING write_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_ticket ALTER touch_datetime TYPE timestamp without time zone USING touch_datetime::timestamp without time zone;

    ALTER TABLE quickbooks_user ALTER write_datetime TYPE timestamp without time zone USING write_datetime::timestamp without time zone;
    ALTER TABLE quickbooks_user ALTER touch_datetime TYPE timestamp without time zone USING touch_datetime::timestamp without time zone;
    ```



### IPP/IntuitAnywhere:
- IntuitAnywhere: Disconnect and reconnect methods did not work with OAuth2
- Driver: Handle null values for oauth_access_token and oauth_refresh_token by using !empty instead of strlen to appease php strict typing.
- Remove some Oauth1 code.  OAuth 1.0 will be disabled as of December 17, 2019. (Maybe this should be added back for a few months)
- Changes to example_app_ipp_v3:
  * **Purchase Check Add example fails with "Call to undefined method QuickBooksPhpDevKit\IPP\Service\Purchase::addLine()"**
  * All other example pages work (but some ids might require user-specific values for delete/modify)



# Original consolibyte README below
_________________________________________

The package you've downloaded contains code and documentation for connecting various versions and editions of QuickBooks to PHP, allowing your PHP applications to do fancy things like:

- Automatically send orders placed on your website to QuickBooks Online or QuickBooks for Windows
- Charge credit cards using Intuit Payments / QuickBooks Merchant Services
- Connect to QuickBooks v3 APIs via OAuth
- Get access to QuickBooks reports
- Pull information out of QuickBooks and display it online
- Connect to all Microsoft Windows versions of QuickBooks
- Connect to all QuickBooks Online versions
- Authenticate via OAuth
- etc. etc. etc.

Almost anything you can do in the QuickBooks GUI, in QuickBooks Online Edition, and with QuickBooks Merchant Service can be accomplished via this framework.

## Quick Start Guides

* QuickBooks FOR WINDOWS (via QuickBooks Web Connector) - read the [quick start guide for the Web Connector/QuickBooks for Windows](http://www.consolibyte.com/docs/index.php/PHP_DevKit_for_QuickBooks_-_Quick-Start)

* QuickBooks ONLINE (via Intuit Partner Platform/Intuit Anywhere) - read the [quick start guide for Intuit Partner Platform/QuickBooks Online] (http://www.consolibyte.com/docs/index.php/PHP_DevKit_for_QuickBooks_-_Intuit_Partner_Platform_Quick-Start)


## OAuth 1.0 to OAuth 2.0 migration

You can find information on how to migrate your app from OAuth v1.0 to OAuth v2.0 below. We are also working on getting OpenID Connect and an automated token migration process ready -- coming soon.

* <https://github.com/consolibyte/quickbooks-php/blob/master/README_OAUTHV1_TO_OAUTHV2.md>

## Updates and Improvements

Please follow me on Twitter to be notified about updates/improvements:

- https://twitter.com/keith_palmer_jr


## Support

If you have questions, suggestions, or find a bug, the absolute best way to get support, report bugs, or ask for help is to ask on the forums:

- http://stackoverflow.com/ (This is the best place to get support -- *make sure you post your code*)
- https://intuitpartnerplatform.lc.intuit.com/


## Examples

You will find examples in the docs/ folder.


### Examples for QuickBooks ONLINE

If you are using *QuickBooks ONLINE*, then you need to look in this folder for examples:

* docs/partner_platform/example_app_ipp_v3/

Make sure you look at the [quick start guide for Intuit Partner Platform/QuickBooks Online] (http://www.consolibyte.com/docs/index.php/PHP_DevKit_for_QuickBooks_-_Intuit_Partner_Platform_Quick-Start)


### Examples for QuickBooks FOR WINDOWS

If you are using *QuickBooks FOR WINDOWS*, then you need to look in this folder for examples:

* docs/web_connector/

Make sure you look at the [quick start guide for the Web Connector/QuickBooks for Windows](http://www.consolibyte.com/docs/index.php/PHP_DevKit_for_QuickBooks_-_Quick-Start)


### Additional Info

There is additional documentation and additional examples on our legacy and new wikis:

- http://wiki.consolibyte.com/wiki/doku.php/quickbooks     (legacy)
- http://www.consolibyte.com/docs/index.php/QuickBooks     (new wiki)




-------------------------------------
###Keith Palmer###
- Follow me on Twitter for updates: https://twitter.com/keith_palmer_jr
