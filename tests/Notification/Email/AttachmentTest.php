<?php

namespace Fazland\Notifire\Tests\Notification\Email;

use Fazland\Notifire\Notification\Email\Attachment;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;

class AttachmentTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        vfsStreamWrapper::unregister();
    }
    
    public function testCreateFromFileShouldLoadContentFromFile()
    {
        $root = vfsStream::setup();

        $file = vfsStream::newFile('attachment.txt');
        $file->setContent('FOO BAR. Test content');
        $root->addChild($file);

        $attachment = Attachment::createFromFile($file->url(), 'text/plain');

        $this->assertEquals('text/plain', $attachment->getContentType());
        $this->assertEquals('attachment.txt', $attachment->getName());
        $this->assertEquals('FOO BAR. Test content', $attachment->getContent());
    }
}
