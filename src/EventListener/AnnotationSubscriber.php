<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Polidog\HypermediaBundle\Annotations\HyperMediaAnnotation;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AnnotationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var bool
     */
    private $halContentType;

    public function __construct(Reader $annotationReader, bool $halContentType)
    {
        $this->annotationReader = $annotationReader;
        $this->halContentType = $halContentType;
    }

    /**
     * @throws \ReflectionException
     */
    public function onKernelController(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        if ($this->halContentType && 'application/hal+json' !== $request->getContentType()) {
            return;
        }

        $controller = $event->getController();
        if (!\is_array($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        } elseif (!\is_array($controller)) {
            return;
        }

        $className = \get_class($controller[0]);
        $object = new \ReflectionClass($className);
        $method = $object->getMethod($controller[1]);

        $annotations = $this->filterAnnotations($this->annotationReader->getClassAnnotations($object));
        $annotations = array_merge($annotations, $this->filterAnnotations($this->annotationReader->getMethodAnnotations($method)));
        $request->attributes->set('_hypermedia_annotations', $annotations);
    }

    private function filterAnnotations(array $annotations): array
    {
        return array_filter($annotations, static function ($annotation) {
            if ($annotation instanceof HyperMediaAnnotation) {
                return $annotation;
            }
        });
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 40],
        ];
    }
}
