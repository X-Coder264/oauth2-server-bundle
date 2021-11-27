<?php

declare(strict_types=1);

namespace League\Bundle\OAuth2ServerBundle\Service;

use League\Event\EmitterInterface;
use League\Event\Event;
use League\Event\EventInterface;
use League\Event\GeneratorInterface;
use League\Event\ListenerProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SymfonyEventDispatcherEmitter implements EmitterInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function removeListener($event, $listener)
    {
        // nothing to do here

        return $this;
    }

    public function useListenerProvider(ListenerProviderInterface $provider)
    {
        // nothing to do here

        return $this;
    }

    public function removeAllListeners($event)
    {
        // nothing to do here

        return $this;
    }

    public function hasListeners($event): bool
    {
        // nothing to do here

        return true;
    }

    public function getListeners($event): array
    {
        // nothing to do here

        return [];
    }

    /**
     * @param string|EventInterface $event
     */
    public function emit($event): EventInterface
    {
        $preparedEvent = $this->prepareEvent($event);

        $this->eventDispatcher->dispatch($preparedEvent, $preparedEvent->getName());

        return $preparedEvent;
    }

    /**
     * @return EventInterface[]
     */
    public function emitBatch(array $events): array
    {
        $results = [];

        /** @var EventInterface|string $event */
        foreach ($events as $event) {
            $results[] = $this->emit($event);
        }

        return $results;
    }

    /**
     * @return EventInterface[]
     */
    public function emitGeneratedEvents(GeneratorInterface $generator): array
    {
        $events = $generator->releaseEvents();

        return $this->emitBatch($events);
    }

    public function addListener($event, $listener, $priority = self::P_NORMAL)
    {
        // nothing to do here

        return $this;
    }

    public function addOneTimeListener($event, $listener, $priority = self::P_NORMAL)
    {
        // nothing to do here

        return $this;
    }

    /**
     * Prepare an event for emitting.
     *
     * @param string|EventInterface $event
     */
    private function prepareEvent($event): EventInterface
    {
        $event = $this->ensureEvent($event);
        $event->setEmitter($this);

        return $event;
    }

    /**
     * Ensure event input is of type EventInterface or convert it.
     *
     * @param string|EventInterface $event
     *
     * @throws \InvalidArgumentException
     */
    private function ensureEvent($event): EventInterface
    {
        if (\is_string($event)) {
            return Event::named($event);
        }

        if (!$event instanceof EventInterface) {
            throw new \InvalidArgumentException(sprintf('Events should be provided as %s instances or string, received type: %s', EventInterface::class, \gettype($event)));
        }

        return $event;
    }
}
