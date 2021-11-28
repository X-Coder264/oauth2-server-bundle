<?php

declare(strict_types=1);

namespace League\Bundle\OAuth2ServerBundle\Service;

use League\Event\ListenerAcceptorInterface;
use League\Event\ListenerProviderInterface;
use League\OAuth2\Server\RequestEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class LeagueEventDispatcher implements ListenerProviderInterface
{
    private const LEAGUE_EVENTS = [
        RequestEvent::CLIENT_AUTHENTICATION_FAILED,
        RequestEvent::USER_AUTHENTICATION_FAILED,
        RequestEvent::REFRESH_TOKEN_CLIENT_FAILED,
        RequestEvent::REFRESH_TOKEN_ISSUED,
        RequestEvent::ACCESS_TOKEN_ISSUED,
    ];

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

        foreach (self::LEAGUE_EVENTS as $leageEvent) {
            $listenerAcceptor->addListener($leageEvent, $listener);
        }

        return $this;
    }

    private function dispatchLeagueEventToSymfonyEventDispatcher(RequestEvent $requestEvent): void
    {
        $this->eventDispatcher->dispatch($requestEvent, $requestEvent->getName());
    }
}
