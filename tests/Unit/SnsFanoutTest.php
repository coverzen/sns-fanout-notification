<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification\Tests\Unit;

use Aws\Result;
use Aws\Sns\SnsClient as SnsService;
use Coverzen\Components\SnsFanoutNotification\SnsFanout;
use Coverzen\Components\SnsFanoutNotification\SnsFanoutMessage;
use Illuminate\Support\Facades\Event;
use JsonException;
use PHPUnit\Framework\Attributes\Test;

class SnsFanoutTest extends TestCase
{
    /** @var SnsService */
    protected SnsService $snsService;

    /** @var SnsFanout */
    protected SnsFanout $snsFanout;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->snsService = $this->partialMock(SnsService::class);

        $this->snsFanout = new SnsFanout($this->snsService);
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function it_will_send_a_message_to_the_sns_service(): void
    {
        $messageData = [
            'body' => [
                'message' => 'Test message',
            ],
            'topic' => 'arn:aws:sns:us-east-1:123456789012:MyTopic',
            'attributes' => [
                'Attribute1' => [
                    'DataType' => 'String',
                    'StringValue' => 'Value1',
                ],
            ],
        ];

        $message = new SnsFanoutMessage(
            $messageData['body'],
            $messageData['topic'],
            $messageData['attributes'],
        );

        $this->snsService
            ->shouldReceive('publish')
            ->with([
                'Message' => json_encode($messageData['body'], JSON_THROW_ON_ERROR),
                'TopicArn' => $messageData['topic'],
                'MessageAttributes' => $messageData['attributes'],
            ])
            ->andReturn(new Result(['MessageId' => '12345']));

        $response = $this->snsFanout->send($message);

        // @phpstan-ignore-next-line
        $this->assertInstanceOf(Result::class, $response);
        $this->assertEquals('12345', $response->get('MessageId'));
    }
}
