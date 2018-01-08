<?php declare(strict_types=1);

namespace Fazland\Notifire\Handler\Email;

use Fazland\Notifire\Converter\SwiftMailerConverter;
use Fazland\Notifire\Exception\NotificationFailedException;
use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\NotificationInterface;
use Fazland\Notifire\Result\Result;

/**
 * SwiftMailer handler.
 */
class SwiftMailerHandler extends AbstractMailHandler
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var SwiftMailerConverter
     */
    private $converter;

    /**
     * @param \Swift_Mailer $mailer
     * @param string        $mailerName
     */
    public function __construct(\Swift_Mailer $mailer, string $mailerName)
    {
        $this->mailer = $mailer;

        parent::__construct($mailerName);
    }

    public function setConverter(SwiftMailerConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function notify(NotificationInterface $notification)
    {
        /* @var Email $notification */
        if (null === $this->converter) {
            $this->converter = new SwiftMailerConverter();
        }

        if (! empty($notification->getTo()) || ! empty($notification->getCc()) || ! empty($notification->getBcc())) {
            $email = $this->converter->convert($notification);
            $result = $this->mailer->send($email);

            $res = new Result('swiftmailer', $this->getName(), 0 < $result ? Result::OK : Result::FAIL);
            $res->setResponse($result);
            $notification->addResult($res);

            if (0 === $result) {
                throw new NotificationFailedException('Mailer reported all recipient failed');
            }
        }
    }
}
