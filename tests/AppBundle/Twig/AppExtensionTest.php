<?php

namespace App\AppBundle\Twig\Tests;

use App\AppBundle\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Markup;

class AppExtensionTest extends TestCase
{
    public function testLoadComponent() : void
    {
        $kernel = $this->getMockBuilder(KernelInterface::class)
                       ->getMock();
        $kernel->method('getProjectDir')->willReturn('/path/to/project');

        $extension = new AppExtension($kernel instanceof KernelInterface ? $kernel : null);
        $result = $extension->loadComponent('componentName');

        $this->assertInstanceOf(Markup::class, $result);
    }
}