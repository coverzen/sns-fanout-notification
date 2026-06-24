<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification;

use JsonException;

final class SnsFanoutMessage
{
    /**
     * Creates a new instance of the message.
     *
     * @see https://docs.aws.amazon.com/sns/latest/dg/sns-message-attributes.html
     * @see https://docs.aws.amazon.com/sns/latest/dg/sns-message-attributes.html#SNSMessageAttributes.ListOfAttributes
     * @see https://docs.aws.amazon.com/sns/latest/dg/sns-message-attributes.html#SNSMessageAttributes.MessageStructure
     *
     * @param array<array-key, mixed> $body
     * @param string $topic
     * @param array<array-key, mixed> $attributes
     */
    public function __construct(
        private array $body = [],
        private string $topic = '',
        private array $attributes = [],
    ) {
    }

    /**
     * Sets the message body.
     *
     * @param array<string, mixed> $body the message body
     *
     * @return $this
     */
    public function body(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the message body.
     *
     * @throws JsonException
     *
     * @return string
     */
    public function getMessage(): string
    {
        return json_encode($this->body, JSON_THROW_ON_ERROR);
    }

    /**
     * Sets the topic ARN.
     *
     * @param string $topic the topic ARN
     *
     * @return $this
     */
    public function topic(string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get the topic ARN.
     *
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * Sets the message attributes.
     *
     * @param array<array-key, mixed> $attributes the message attributes
     *
     * @return $this
     */
    public function attributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get the message attributes.
     *
     * @return array<array-key, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
