<?php

namespace Polidog\HypermediaBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
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
     * @param array $params
     */
    public function __construct(array $params)
    {
        foreach (['rel', 'href'] as $target) {
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

    /**
     * @return string
     */
    public function href(array $parameters)
    {
        $srcArray = explode('/', $this->href);
        $urls = [];
        foreach($srcArray as $target) {
            if (preg_match('/\{(.+)\}/', $target, $matches)) {
                if (isset($parameters[$matches[1]])) {
                    $urls[] = $parameters[$matches[1]];
                } else {
                    $urls[] =$target;
                }
            } else {
                $urls[] = $target;
            }
        }
        return implode('/', $urls);
    }

}
