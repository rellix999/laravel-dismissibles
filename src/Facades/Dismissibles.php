<?php

declare(strict_types=1);

namespace Rellix\Dismissibles\Facades;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use Rellix\Dismissibles\Concerns\Dismiss;
use Rellix\Dismissibles\Contracts\Dismisser;
use Rellix\Dismissibles\Models\Dismissible;

class Dismissibles extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dismissibles';
    }

    /**
     * Returns the active dismissible if it exists.
     */
    public static function get(string $name): ?Dismissible
    {
        return Dismissible::active()->firstWhere('name', $name);
    }

    /**
     * Returns all active dismissibles for the given $dismisser.
     * It excludes the dismissibles that are dismissed until a future date.
     *
     * @return Collection<Dismissible>
     */
    public static function getAllThatShouldBeShownTo(Dismisser $dismisser): Collection
    {
        return Dismissible::active()->notDismissedBy($dismisser)->get();
    }

    /**
     * Returns whether the dismissible should be shown to the dismisser at the current moment.
     */
    public static function shouldShow(string $name, Dismisser $dismisser): bool
    {
        $dismissible = self::get($name);

        return $dismissible && !$dismissible->isDismissedBy($dismisser);
    }

    /**
     * Returns a Dismiss object which allows you to dismiss the dismissible for a specified period.
     */
    public static function dismiss(string $name, Dismisser $dismisser): ?Dismiss
    {
        $dismissible = self::get($name);
        if (!$dismissible) {
            return null;
        }

        return new Dismiss($dismisser, $dismissible);
    }

    /**
     * Returns whether a dismissible is currently dismissed by the dismisser.
     */
    public static function isDismissed(string $name, Dismisser $dismisser): bool
    {
        /** @var Dismissible $dismissible */
        $dismissible = Dismissible::firstWhere('name', $name);

        return $dismissible->isDismissedBy($dismisser);
    }
}
