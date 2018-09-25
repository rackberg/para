<?php

namespace Service\Packagist;


use GuzzleHttp\ClientInterface;
use Para\Factory\PackageFactoryInterface;
use Para\Package\ComposerPackageInterface;
use Para\Service\Packagist\Packagist;
use Para\Service\Packagist\PackagistInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PackagistTest.
 *
 * @package Service\Packagist
 */
class PackagistTest extends TestCase
{
    /**
     * The packagist to test.
     *
     * @var PackagistInterface
     */
    private $packagist;

    /**
     * The guzzle http client mock instance.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * The package factory mock instance.
     *
     * @var PackageFactoryInterface
     */
    private $packageFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->httpClient = $this->prophesize(ClientInterface::class);
        $this->packageFactory = $this->prophesize(PackageFactoryInterface::class);

        $this->packagist = new Packagist(
            $this->httpClient->reveal(),
            $this->packageFactory->reveal()
        );
    }

    /**
     * Tests the getPackageVersions() method.
     *
     * This test should prove that an array with versions will be returned for a composer package.
     */
    public function testGetPackageVersions()
    {
        $vendor = 'lrackwitz';
        $package = 'para';
        $uri = 'https://packagist.org/p/' . $vendor . '/' . $package . '.json';

        $responseBody = '{"packages":{"lrackwitz/para":{"1.0.0":{}, "2.0.0":{},"dev-master":{}}}}';

        /** @var ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);

        $this->httpClient->request('GET', $uri)->willReturn($response->reveal());

        $versions = $this->packagist->getPackageVersions($vendor . '/' . $package);

        $this->httpClient->request('GET', $uri)->shouldHaveBeenCalled();
        $response->getStatusCode()->shouldHaveBeenCalled();
        $response->getBody()->shouldHaveBeenCalled();

        $this->assertTrue(is_array($versions), 'Expected that the result is of type array.');
        $this->assertTrue(in_array('1.0.0', $versions), 'Expected that the version 1.0.0 has been found.');
        $this->assertTrue(in_array('2.0.0', $versions), 'Expected that the version 2.0.0 has been found.');
        $this->assertTrue(in_array('dev-master', $versions), 'Expected that the dev-master version has been found.');
    }

    /**
     * Tests the findPackagesByType() method.
     *
     * This test should prove that an array of composer packages will be returned.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testFindPackagesByType()
    {
        $type = 'para-plugin';
        $uri = 'https://packagist.org/search.json?type=' . $type;
        $responseBody = <<< JSON
{
    "results": [
        {
            "name": "lrackwitz/para-alias"
        },
        {
            "name": "lrackwitz/para-sync"
        }
    ]
}
JSON;
        /** @var ResponseInterface $response */
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($responseBody);

        $this->httpClient->request('GET', Argument::type('string'))->willReturn($response->reveal());
        $this->packageFactory
            ->getPackage(Argument::type('array'))
            ->willReturn($this->prophesize(ComposerPackageInterface::class)->reveal());

        $packages = $this->packagist->findPackagesByType($type);

        $this->httpClient->request('GET', Argument::type('string'))->shouldHaveBeenCalled();
        $this->packageFactory->getPackage(Argument::type('array'))->shouldHaveBeenCalled();

        $response->getStatusCode()->shouldHaveBeenCalled();
        $response->getBody()->shouldHaveBeenCalled();

        $this->assertTrue(is_array($packages), 'Expected that an array has been returned.');
        $this->assertTrue($packages[0] instanceof ComposerPackageInterface, 'Expected that a composer package has been returned.');
        $this->assertTrue($packages[1] instanceof ComposerPackageInterface, 'Expected that a composer package has been returned.');
    }

}