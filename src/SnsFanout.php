<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification;

use Aws\Result;
use Aws\Sns\SnsClient;
use JsonException;

final class SnsFanout
{
    /**
     * Create a new instance of the class.
     *
     * @param SnsClient $sns
     */
    public function __construct(protected SnsClient $sns)
    {
    }

    /**
     * Send the message to the specified SNS topic (fanout).
     *
     * @param SnsFanoutMessage $message the message to send
     *
     * @throws JsonException
     * @return Result
     */
    public function send(SnsFanoutMessage $message): Result
    {
        /** @var array<string, mixed> $parameters */
        $parameters = [
            'Message' => $message->getMessage(),
            'MessageAttributes' => $message->getAttributes(),
            'TopicArn' => $message->getTopic(),
        ];

        return $this->sns->publish($parameters);
    }
}
