User Agent Parser > UAParser
============================

## Parse any user agent string by PHP ##
- Detect the device (mobile|tablet|smartTV|desktop or service)
- The device contain the brand (like Samsung), and contain the phone or tablet type (like Prestigio PAP5400DUO).
- Detect visitor Operation System like Windows, Linux, OSX, Android...
- The OS info contain the OS version (like Windows NT5.1 or Android 4.2.2).
- Detect visitor Browser/Client like Chrome, Firefox, Outlook...
- The browser info contain tha browser or client version (like MSOffice 15 or Chrome 38...)

## Usage ##
````php
$UAP = new dynamicart\UAParser('Mozilla/5.0 (Linux; U; Android 5.0; hu-hu; LG-D855 Build/LRX21R.A1421812393) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
$UAP->getParsedData();
````

or

````php
$UAP = new dynamicart\UAParser();
$UAP->setUA('Mozilla/5.0 (Linux; U; Android 5.0; hu-hu; LG-D855 Build/LRX21R.A1421812393) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30');
$UAP->getParsedData();
````

## License ##
The mobile/tablet/os/browser patterns based on Mobile Detect Library 2.8.17 (http://mobiledetect.net), but this was just the start point.

The patterns are continuously grow and change.

@author: János Szentgyörgyi <puttocska@gmail.com>

@license: Code and contributions have 'MIT License'
https://github.com/dynamicart/UAParser/blob/master/LICENSE

GitHub Repo: https://github.com/dynamicart/UAParser

## What's new ##

@version: 0.2.0
+ smartTV detection
+ pattern updates
+ recheck the detected device, if parsed device is 'desktop' and parsed OS is not desktopOS, then device set to 'mobile'

version: 0.1.3
+ mobile & tablet patterns updated, 
+ mobile patterns are sorted,
+ device type is fixed  

## Warning ##
This script version is very early! Don't trust in the result...
