<?php
declare(strict_types=1);

namespace FH\Bundle\CookieGuardBundle\Tests\Twig;

use FH\Bundle\CookieGuardBundle\Twig\CookieGuardExtension;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Evert Harmeling <evert@freshheads.com>
 */
final class CookieGuardExtensionTest extends \PHPUnit\Framework\TestCase
{
    private const COOKIE_NAME = 'test-cookie';

    public function testCookieSettingsAreNotAccepted(): void
    {
        // arrange
        $extension = $this->createCookieGuardExtension();

        // act && assert
        Assert::assertFalse($extension->cookieSettingsAreAccepted());
    }

    public function testCookieSettingsAreAccepted(): void
    {
        // arrange
        $request = $this->createRequestWithCookie();
        $extension = $this->createCookieGuardExtension($this->createRequestStackMock($request));

        // act && assert
        Assert::assertTrue($extension->cookieSettingsAreAccepted());
    }

    public function testCookieSettingsAreNotSubmitted(): void
    {
        // arrange
        $extension = $this->createCookieGuardExtension();

        // act && assert
        Assert::assertFalse($extension->cookieSettingsSubmitted());
    }

    public function testCookieSettingsAreSubmitted(): void
    {
        // arrange
        $request = $this->createRequestWithCookie();
        $extension = $this->createCookieGuardExtension($this->createRequestStackMock($request));

        // act && assert
        Assert::assertTrue($extension->cookieSettingsSubmitted());
    }

    public function testShowIfCookieIsNotAccepted(): void
    {
        // arrange
        $extension = $this->createCookieGuardExtension();

        // act && assert
        Assert::assertEquals(
            sprintf('<meta class="js-cookie-guarded" data-content="%s" />', 'cookie not accepted'),
            $extension->showIfCookieAccepted('cookie not accepted')
        );
    }

    public function testShowIfCookieAccepted(): void
    {
        // arrange
        $extension = $this->createCookieGuardExtension(null, $this->createTwigEnvironmentMock('cookie accepted', true));

        // act && assert
        Assert::assertEquals('cookie accepted', $extension->showIfCookieAccepted('cookie accepted'));
    }

    private function createRequestWithCookie(): Request
    {
        return new Request([], [], [], [
            self::COOKIE_NAME => true
        ]);
    }

    private function createCookieGuardExtension(RequestStack $requestStack = null, MockObject $twigEnvironment = null): CookieGuardExtension
    {
        return new CookieGuardExtension(
            $requestStack ?? $this->createRequestStackMock(),
            $twigEnvironment ?? $this->createTwigEnvironmentMock(),
            self::COOKIE_NAME
        );
    }

    private function createRequestStackMock(Request $request = null): RequestStack
    {
        $requestStack = new RequestStack();
        $requestStack->push($request ?? Request::createFromGlobals());

        return $requestStack;
    }

    /**
     * @return MockObject|\Twig_Environment
     */
    private function createTwigEnvironmentMock(string $content = 'cookie not accepted', bool $show = false): MockObject
    {
        $twigEnvironmentMock = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'render'
            ])
            ->getMock();

        if (!$show) {
            $content = sprintf('<meta class="js-cookie-guarded" data-content="%s" />', $content);
        }

        $twigEnvironmentMock
            ->method('render')
            ->willReturn($content);

        return $twigEnvironmentMock;
    }
}
