<?php

namespace Polidog\HypermediaBundle;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class EmbedRequestExecutor
{
    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * @param HttpKernelInterface $kernel
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @throws \Exception
     */
    public function execute(Request $masterRequest, string $src) :array
    {
        $request =  Request::create($src, $masterRequest->getMethod(), $masterRequest->query->all());
        $response = $this->kernel->handle($request, HttpKernelInterface::SUB_REQUEST);
        if ($response->getStatusCode() !== 200) {
            return [];
        }
        return json_decode($response->getContent(), true);
    }
}
