<?php

namespace Fuz\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleSubscriber implements EventSubscriber
{

    protected $context;
    protected $templates;
    protected $tags;

    public function getSubscribedEvents()
    {
        return array (
                'prePersist',
                'postPersist',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof Fiddle)
        {
            $this->context = $object->getContext();
            $object->setContext(null);
            $this->templates = $object->getTemplates();
            $object->setTemplates(new ArrayCollection());
            $this->tags = $object->getTags();
            $object->setTags(new ArrayCollection());
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        if ($object instanceof Fiddle)
        {
            $this->context->setFiddle($object);
            $om->persist($this->context);

            foreach ($this->templates as $template)
            {
                $template->setFiddle($object);
                $om->persist($template);
            }

            foreach ($this->tags as $tag)
            {
                $tag->setFiddle($object);
                $om->persist($tag);
            }

            $om->flush();
        }
    }

}
