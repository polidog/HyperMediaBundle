<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Polidog\HyperMediaBundle\Exception\SrcResolveException;
use Polidog\HyperMediaBundle\UrlGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Embed implements HyperMediaAnnotation
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $src;

    /**
     * @var string
     */
    private $name;

    public function __construct(array $params)
    {
        foreach (['rel', 'name', 'src'] as $target) {
            if (isset($params[$target])) {
                $this->$target = $params[$target];
            }
        }
    }

    public function getRel(): string
    {
        return $this->rel;
    }

    /**
     * @throws SrcResolveException
     */
    public function src(UrlGenerator $generator, array $parameters = []): string
    {
        if (null !== $this->name) {
            return $generator->nameResolve($this->name, $parameters);
        }
        if (null !== $this->src) {
            return $generator->pathResolve($this->src, $parameters);
        }

        throw new SrcResolveException('Name and src is null.');
    }
}
