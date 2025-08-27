# Laravel notification system for AWS SNS as fanout

A Laravel package for sending notifications via AWS SNS (Simple Notification Service) fanout messaging.

## Description

This package provides a custom notification channel for Laravel that enables sending notifications through AWS SNS fanout topics. It allows you to broadcast messages to multiple subscribers simultaneously using AWS SNS topic fanout functionality.

## Requirements

- PHP 8.2 or 8.3
- Laravel 11.x or 12.x
- AWS SDK for PHP ^3.353

## Installation

Install the package via Composer:

```bash
composer require coverzen/sns-fanout-notification
```

The service provider will be automatically registered via Laravel's package discovery.

## Configuration

Configure your AWS SNS credentials in `config/services.php`:

```php
'sns' => [
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    'token' => env('AWS_SESSION_TOKEN'), // Optional for temporary credentials
],
```

Add the corresponding environment variables to your `.env` file:

```env
AWS_ACCESS_KEY_ID=your-access-key-id
AWS_SECRET_ACCESS_KEY=your-secret-access-key
AWS_DEFAULT_REGION=us-east-1
```

## Usage

### Creating a Notification

Create a notification class that uses the SNS fanout channel:

```php
<?php

use Illuminate\Notifications\Notification;
use Coverzen\Components\SnsFanoutNotification\SnsFanoutMessage;

class OrderNotification extends Notification
{
    public function via($notifiable)
    {
        return ['snsFanout'];
    }

    public function toSnsFanout($notifiable)
    {
        return (new SnsFanoutMessage())
            ->topic('arn:aws:sns:us-east-1:123456789012:order-updates')
            ->body([
                'event' => 'order_created',
                'order_id' => $this->order->id,
                'customer_id' => $this->order->customer_id,
                'timestamp' => now()->toISOString(),
            ])
            ->attributes([
                'event_type' => [
                    'DataType' => 'String',
                    'StringValue' => 'order_created'
                ]
            ]);
    }
}
```

### Alternative Array Format

You can also return an array from the `toSnsFanout` method:

```php
public function toSnsFanout($notifiable)
{
    return [
        'topic' => 'arn:aws:sns:us-east-1:123456789012:order-updates',
        'body' => [
            'event' => 'order_created',
            'order_id' => $this->order->id,
        ],
        'attributes' => [
            'event_type' => [
                'DataType' => 'String',
                'StringValue' => 'order_created'
            ]
        ]
    ];
}
```

### Sending Notifications

Send the notification using Laravel's standard notification methods:

```php
// Send to a specific user
$user->notify(new OrderNotification($order));

// Send to multiple users
Notification::send($users, new OrderNotification($order));

// Send using the Notification facade
Notification::route('snsFanout', null)
    ->notify(new OrderNotification($order));
```

### Message Structure

The `SnsFanoutMessage` class provides the following methods:

- `topic(string $topicArn)`: Set the SNS topic ARN
- `body(array $body)`: Set the message body (will be JSON encoded)
- `attributes(array $attributes)`: Set message attributes for filtering

## Message Attributes

Message attributes follow the AWS SNS format. Each attribute should have a `DataType` and a value field:

```php
[
    'attribute_name' => [
        'DataType' => 'String|Number|Binary',
        'StringValue' => 'value', // for String or Number type
        'BinaryValue' => 'value', // for Binary type
    ]
]
```

## Error Handling

The package automatically dispatches Laravel's `NotificationFailed` event when a notification fails to send. You can listen for this event to handle failures:

```php
Event::listen(\Illuminate\Notifications\Events\NotificationFailed::class, function ($event) {
    if ($event->channel === 'snsFanout') {
        Log::error('SNS notification failed', [
            'notifiable' => $event->notifiable,
            'notification' => $event->notification,
            'data' => $event->data,
        ]);
    }
});
```

## License

This package is owned by Coverzen under MIT License.

## Contributing

Please follow the established coding standards and ensure all tests pass before submitting changes.
