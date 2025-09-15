<?php declare(strict_types=1);

namespace Coverzen\Components\SnsFanoutNotification\Tests\Unit;

use Coverzen\Components\SnsFanoutNotification\SnsFanout;
use Coverzen\Components\SnsFanoutNotification\SnsFanoutChannel;
use Coverzen\Components\SnsFanoutNotification\SnsFanoutMessage;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use stdClass;

#[CoversClass(SnsFanoutChannel::class)]
class SnsFanoutChannelTest extends TestCase
{
    /** @var SnsFanout */
    protected SnsFanout $snsFanout;

    /** @var SnsFanoutChannel */
    protected SnsFanoutChannel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->snsFanout = $this->partialMock(SnsFanout::class);
        $this->channel = new SnsFanoutChannel($this->snsFanout);
    }

    #[Test]
    public function it_will_not_send_a_message_without_known_receiver(): void
    {
        /** @var Notification $notification */
        $notification = $this->partialMock(Notification::class);

        $this->assertNull(
            $this->channel->send(
                new class {
                    use Notifiable;
                },
                $notification
            )
        );

        Event::assertDispatched(NotificationFailed::class);
    }

    #[Test]
    public function it_will_send_a_message_to_the_result_of_the_route_method_of_the_notifiable(): void
    {
        /** @var SnsFanoutMessage $message */
        $message = new SnsFanoutMessage(
            [
                'message' => 'Test message',
            ],
            'arn:aws:sns:eu-south-1:123456789012:TestTopic',
            []
        );

        /** @var Notification&MockInterface $notification */
        $notification = $this->partialMock(Notification::class);
        $notification->expects('toSnsFanout')
                     ->andReturn($message);

        $this->snsFanout->expects('send')
                        ->atLeast()
                        ->once()
                        ->with($message);

        $this->channel->send(
            new class {
                use Notifiable;
            },
            $notification
        );
    }

    #[Test]
    public function it_will_convert_a_string_to_a_sns_fanout_message(): void
    {
        /** @var Notification&MockInterface $notification */
        $notification = $this->partialMock(Notification::class);
        $notification->expects('toSnsFanout')
                     ->andReturn([
                         'body' => [
                             'message' => 'Test message',
                         ],
                         'topic' => 'arn:aws:sns:eu-south-1:123456789012:TestTopic',
                         'attributes' => [],
                     ]);

        $this->snsFanout->expects('send')
                        ->atLeast()
                        ->once()
                        ->with(SnsFanoutMessage::class);

        $this->channel->send(
            new class {
                use Notifiable;
            },
            $notification
        );
    }

    #[Test]
    public function it_will_throw_an_exception_if_the_message_is_not_a_valid_object(): void
    {
        /** @var Notification&MockInterface $notification */
        $notification = $this->partialMock(Notification::class);
        $notification->expects('toSnsFanout')
                     ->andReturn(new stdClass());

        $this->channel->send(
            new class {
                use Notifiable;
            },
            $notification
        );

        Event::assertDispatched(NotificationFailed::class);
    }
}
