<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification\Exceptions;

use Coverzen\Components\SnsFanoutNotification\SnsFanoutMessage;
use Exception;

final class CouldNotSendNotification extends Exception
{
    /**
     * Create a new exception instance for an invalid message object.
     *
     * @param mixed $message the message that was attempted to be sent
     *
     * @return static
     */
    public static function invalidMessageObject(mixed $message): CouldNotSendNotification
    {
        $type = is_object($message) ? $message::class : gettype($message);

        return new CouldNotSendNotification(
            'Notification was not sent. The message should be a instance of `' . SnsFanoutMessage::class . "` and a `{$type}` was given."
        );
    }
}
