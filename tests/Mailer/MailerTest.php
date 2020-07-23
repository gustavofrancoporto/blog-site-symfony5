<?php


namespace App\Tests\Mailer;


use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerTest extends TestCase
{
    public function testConfirmationEmail()
    {
        $mailFrom = 'me@domain.com';
        $mailBody = 'This is the email body!';
        $user = new User();
        $user->setEmail('john@doe.com');

        $mailerInterfaceMock = $this->getMockBuilder(MailerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mailerInterfaceMock->expects($this->once())->method('send')
            ->with($this->callback(function(/** @var Email $email*/ $email) use ($mailFrom, $mailBody, $user) {

                return $mailFrom === $email->getFrom()[0]->getAddress() &&
                    $user->getEmail() === $email->getTo()[0]->getAddress() &&
                    'Welcome!' === $email->getSubject() &&
                    $mailBody === $email->getHtmlBody();
            }));

        $twigMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $twigMock->expects($this->once())->method('render')
            ->with('email/registration.html.twig',[ 'user' => $user ])
            ->willReturn($mailBody);


        $mailer = new Mailer($mailerInterfaceMock, $twigMock, $mailFrom);
        $mailer->sendConfirmationEmail($user);
    }
}