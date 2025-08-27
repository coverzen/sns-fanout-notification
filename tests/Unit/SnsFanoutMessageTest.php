<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification\Tests\Unit;

use Coverzen\Components\SnsFanoutNotification\SnsFanoutMessage;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(SnsFanoutMessage::class)]
class SnsFanoutMessageTest extends TestCase
{
    /**
     * @throws JsonException
     */
    #[Test]
    public function it_can_be_instantiated_with_a_array_body_and_json_encode_it_on_get(): void
    {
        $body = [
            'message' => 'Test message',
            'count' => 1,
            'data' => [
                'key' => 'value',
            ],
        ];

        $attributes = [
            'Attribute1' => [
                'DataType' => 'String',
                'StringValue' => 'Value1',
            ],
        ];

        $topic = 'arn:aws:sns:eu-south-1:123456789012:MyTopic';

        $message = new SnsFanoutMessage(
            body: $body,
            topic: $topic,
            attributes: $attributes,
        );

        $this->assertSame(json_encode($body, JSON_THROW_ON_ERROR), $message->getMessage());
        $this->assertSame($attributes, $message->getAttributes());
        $this->assertSame($topic, $message->getTopic());
    }
}
