<?php

declare(strict_types=1);

namespace Polidog\HypermediaBundle\EventListener;

use Polidog\HypermediaBundle\Annotations\Embed;
use Polidog\HypermediaBundle\Annotations\Link;
use Polidog\HypermediaBundle\EmbedRequestExecutor;
use Polidog\HyperMediaBundle\UrlGenerator;
use Polidog\SimpleApiBundle\Event\ViewParameterEvent;
use Polidog\SimpleApiBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewListenerSubscriber implements EventSubscriberInterface
{
    /**
     * @var EmbedRequestExecutor
     */
    private $embedRequestExecutor;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    public function __construct(EmbedRequestExecutor $embedRequestExecutor, UrlGenerator $urlGenerator)
    {
        $this->embedRequestExecutor = $embedRequestExecutor;
        $this->urlGenerator = $urlGenerator;
    }

    public function onViewParameters(ViewParameterEvent $event): void
    {
        $request = $event->getRequest();
        $parameters = $event->getParameters();

        $annotations = $request->attributes->get('_hypermedia_annotations');
        $parameters['_links']['self'] = $this->urlGenerator->selfResource($request);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Embed) {
                $parameters['_embedded'][$annotation->getRel()] = $this->embedRequestExecutor->execute($request, $annotation->src($this->urlGenerator, $request->attributes->all()));
            }
            if ($annotation instanceof Link) {
                $parameters['_links'][$annotation->getRel()] = $annotation->href($this->urlGenerator, $request->attributes->all());
            }
        }

        $event->setParameters($parameters);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::VIEW_PARAMETERS => 'onViewParameters',
        ];
    }
}
