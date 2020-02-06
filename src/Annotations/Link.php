<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use Polidog\HyperMediaBundle\UrlGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class Link implements HyperMediaAnnotation
{
    /**
     * @var string
     */
    private $rel;

    /**
     * @var string
     */
    private $href;

    /**
     * @var string
     */
    private $name;

    public function __construct(array $params)
    {
        foreach (['rel', 'name', 'href'] as $target) {
            if (isset($params[$target])) {
                $this->$target = $params[$target];
            }
        }
    }

    /**
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    public function href(UrlGenerator $generator, array $parameters): string
    {
        if (null !== $this->name) {
            return $generator->nameResolve($this->name, $parameters);
        }
        if (null !== $this->href) {
            return $generator->pathResolve($this->href, $parameters);
        }
    }
}
