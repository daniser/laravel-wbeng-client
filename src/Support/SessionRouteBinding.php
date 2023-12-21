<?php

declare(strict_types=1);

namespace TTBooking\WBEngine\Support;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use TTBooking\WBEngine\Contracts\Session;
use TTBooking\WBEngine\Exceptions\SessionNotFoundException;
use TTBooking\WBEngine\Facades\Storage;

class SessionRouteBinding
{
    public static function resolveForRoute(Container $container, Route $route, callable $default): Route
    {
        $default();

        $parameters = $route->parameters();

        foreach ($route->signatureParameters(['subClass' => Session::class]) as $parameter) {
            if (! $parameterName = static::getParameterName($parameter->getName(), $parameters)) {
                continue;
            }

            $parameterValue = $parameters[$parameterName];

            $session = Storage::session($parameterValue);

            if ($session->history()->isEmpty()) {
                throw new SessionNotFoundException("Session [$parameterValue] not found");
            }

            $route->setParameter($parameterName, $session);
        }

        return $route;
    }

    /**
     * Return the parameter name if it exists in the given parameters.
     *
     * @param  array<string, string|null>  $parameters
     */
    protected static function getParameterName(string $name, array $parameters): ?string
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }

        return null;
    }
}
