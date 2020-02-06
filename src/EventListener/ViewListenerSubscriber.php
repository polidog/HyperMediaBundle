<?php

namespace Polidog\HypermediaBundle\EventListener;


use Polidog\HypermediaBundle\Annotations\Embed;
use Polidog\HypermediaBundle\Annotations\Link;
use Polidog\HypermediaBundle\EmbedRequestExecutor;
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
     * @param EmbedRequestExecutor $embedRequestExecutor
     */
    public function __construct(EmbedRequestExecutor $embedRequestExecutor)
    {
        $this->embedRequestExecutor = $embedRequestExecutor;
    }

    public function onViewParameters(ViewParameterEvent $event) :void
    {
        $request = $event->getRequest();
        $parameters = $event->getParameters();

        $annotations = $request->attributes->get('_hypermedia_annotations');
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Embed) {
                $parameters['_embedded'][$annotation->getRel()] = $this->embedRequestExecutor->execute($request, $annotation->src($request->attributes->all()));
            }
            if ($annotation instanceof Link) {
                $parameters['_link'][$annotation->getRel()] = $annotation->href($request->attributes->all());
            }
        }

        $event->setParameters($parameters);
    }

    public static function getSubscribedEvents() :array
    {
        return [
            Events::VIEW_PARAMETERS => 'onViewParameters'
        ];
    }
}
