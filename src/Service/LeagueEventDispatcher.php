<?php

declare(strict_types=1);

namespace League\Bundle\OAuth2ServerBundle\Service;

use League\Event\EventInterface;
use League\Event\ListenerAcceptorInterface;
use League\Event\ListenerProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class LeagueEventDispatcher implements ListenerProviderInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function provideListeners(ListenerAcceptorInterface $listenerAcceptor)
    {
        $listener = \Closure::fromCallable([$this, 'dispatchLeagueEventToSymfonyEventDispatcher']);

        $listenerAcceptor->addListener('*', $listener);

        return $this;
    }

    private function dispatchLeagueEventToSymfonyEventDispatcher(EventInterface $event): void
    {
        $this->eventDispatcher->dispatch($event, $event->getName());
    }
}
