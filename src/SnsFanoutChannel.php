<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification;

use Aws\Result;
use Coverzen\Components\SnsFanoutNotification\Exceptions\CouldNotSendNotification;
use Exception;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

final class SnsFanoutChannel
{
    public function __construct(private SnsFanout $snsFanout)
    {
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @return Result|null
     */
    public function send(mixed $notifiable, Notification $notification): ?Result
    {
        try {
            /** @var SnsFanoutMessage $message */
            $message = $this->getMessage($notifiable, $notification);

            return $this->snsFanout->send($message);
        } catch (Exception $e) {
            Event::dispatch(new NotificationFailed(
                $notifiable,
                $notification,
                'snsFanout',
                ['message' => $e->getMessage(), 'exception' => $e]
            ));

            return null;
        }
    }

    /**
     * Get the SNS Message object.
     *
     * @param mixed $notifiable
     * @param Notification $notification
     *
     * @throws CouldNotSendNotification
     * @return SnsFanoutMessage
     */
    private function getMessage(mixed $notifiable, Notification $notification): SnsFanoutMessage
    {
        /** @var SnsFanoutMessage|array<string, mixed>|null $message */
        /* @phpstan-ignore method.notFound */
        $message = $notification->toSnsFanout($notifiable);

        if ($message) {
            if (is_array($message)) {
                return new SnsFanoutMessage(
                    /* @phpstan-ignore argument.type */
                    Arr::get($message, 'body', []),
                    /* @phpstan-ignore argument.type */
                    Arr::get($message, 'topic', ''),
                    /* @phpstan-ignore argument.type */
                    Arr::get($message, 'attributes', [])
                );
            }

            if ($message instanceof SnsFanoutMessage) {
                return $message;
            }
        }

        throw CouldNotSendNotification::invalidMessageObject($message);
    }
}
