<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Notification\Email;

use Fazland\Notifire\Notification\Email\Attachment;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        vfsStreamWrapper::unregister();
    }

    public function testCreateFromFileShouldLoadContentFromFile(): void
    {
        $root = vfsStream::setup();

        $file = vfsStream::newFile('attachment.txt');
        $file->setContent('FOO BAR. Test content');
        $root->addChild($file);

        $attachment = Attachment::createFromFile($file->url(), 'text/plain');

        self::assertEquals('text/plain', $attachment->getContentType());
        self::assertEquals('attachment.txt', $attachment->getName());
        self::assertEquals('FOO BAR. Test content', $attachment->getContent());
    }
}
