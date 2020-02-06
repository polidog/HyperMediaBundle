<?php

namespace Polidog\HypermediaBundle\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Hal implements HyperMediaAnnotation
{
}
