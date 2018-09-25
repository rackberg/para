<?php

namespace Para\Service\Packagist;

use GuzzleHttp\ClientInterface;
use Para\Factory\PackageFactoryInterface;

/**
 * Implementation of a PackagistInterface.
 *
 * @package Para\Service\Packagist
 */
class Packagist implements PackagistInterface
{
    const GET_PACKAGE_META_DATA_URL = 'https://packagist.org/p/%s.json';
    const SEARCH_PACKAGES_BY_TYPE = 'https://packagist.org/search.json?type=%s';

    /**
     * The guzzle http client.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * The composer package factory interface.
     *
     * @var PackageFactoryInterface
     */
    private $packageFactory;

    /**
     * Packagist constructor.
     *
     * @param ClientInterface $httpClient The guzzle http client.
     * @param PackageFactoryInterface $packageFactory The composer package factory.
     */
    public function __construct(
        ClientInterface $httpClient,
        PackageFactoryInterface $packageFactory
    ) {
       $this->httpClient = $httpClient;
       $this->packageFactory = $packageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function findPackagesByType($type): array
    {
        $packages = [];

        $body = $this->getJsonResponseBody('GET', sprintf(self::SEARCH_PACKAGES_BY_TYPE, $type));
        if (!empty($body->results)) {
            foreach ($body->results as $result) {
                $versions = $this->getPackageVersions($result->name);

                // Get the highest stable version.
                $result->version = $this->getHighestVersion($versions);

                $packages[] = $this->packageFactory->getPackage((array)$result);
            }
        }

        return $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageVersions($package_name): array
    {
        $versions = [];

        $body = $this->getJsonResponseBody('GET', sprintf(self::GET_PACKAGE_META_DATA_URL, $package_name));
        if (isset($body->packages->{$package_name})) {
            $versions = array_keys((array)$body->packages->{$package_name});
        }

        return $versions;
    }

    /**
     * Returns the json decoded response body.
     *
     * @param string $method The request method.
     * @param string $uri The request uri.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException When trying to send the request failed.
     */
    private function getJsonResponseBody($method, $uri)
    {
        $body = [];

        $response = $this->httpClient->request($method, $uri);
        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody());
        }

        return $body;
    }

    /**
     * Returns the highest version of an array of versions.
     *
     * @param array $versions The array with versions available.
     *
     * @return string The highest version string.
     */
    private function getHighestVersion(array $versions = []) {
        $highest_version = 0;
        foreach ($versions as $version_number) {
            if ($version_number === 'dev-master') continue;
            if (str_replace('.', '', $version_number)+0 > $highest_version) {
                $highest_version = $version_number;
            }
        }
        if ($highest_version == 0) {
            return 'dev-master';
        }

        return $highest_version;
    }
}