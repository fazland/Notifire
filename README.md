Notifire  
========
[![Build Status](https://travis-ci.org/fazland/Notifire.svg?branch=master)](https://travis-ci.org/fazland/Notifire)

Notifire is a PHP library that centralizes the management of notifications (e-mails, sms, push notifications, etc.). 

Requirements
------------
- `php` >= 7.2
- `symfony/event-dispatcher` >= 4.3
- `symfony/options-resolver` >= 4.3

Installation
------------
The suggested installation method is via [composer](https://getcomposer.org/):

```sh
$ composer require fazland/notifire
```

Using Notifire
--------------
Every notification in Notifire triggers an Event (the `NotifyEvent`) which will be handled by an instance of `NotifyEventSubscriber` (later named by `handlers`).
Those notifications must implement `NotificationInterface` and registered with `Notifire::addNotification()` in order to be read by Notifire.

Notifire provides 2 standard implementations (`Email` and `Sms`) and theirs `handlers` (the defaults are `SwiftMailerHandler` and `TwilioHandler`).

Notifire is really simple to use:

### Initialization
First of all Notifire has to be initialized. Two ways:
1) run in order to autoconfigure the e-mail with [SwiftMailer](https://github.com/swiftmailer/swiftmailer) as its `Handler`
```php
Notifire::create();
```

2) custom configuration with `NotifireBuilder` by registering the notifications and the desired instance of `EventDispatcherInterface`
```php
require_once('vendor/autoload.php');

$dispatcher = new EventDispatcher();

$builder = NotifireBuilder::create()
    ->setDispatcher($dispatcher)
;

$builder->addHandler(new SwiftMailerHandler($mailer, 'mailer_one'));
$builder->addNotification('email', Email::class);

$builder->initialize();
```

Now you're ready!
To create an `Email` just use `Notifire::email()`, fill the fields like `from`, `to`, `parts` etc. and then use `Email::send()`:
```php
// Use 'mailer_one' handler to send this message
$email = Notifire::email('mailer_one');

$email
    ->addFrom('test@fazland.com')
    ->addTo('info@example.org')
    ->setSubject('Only wonderful E-mails with Notifire!')
    ->addPart(Part::create($body, 'text/html'))
    ->send()
;
```

Contributing
------------
Contributions are welcome. Feel free to open a PR or file an issue here on GitHub!

License
-------
Notifire is licensed under the MIT License - see the [LICENSE](https://github.com/fazland/Notifire/blob/master/LICENSE) file for details
