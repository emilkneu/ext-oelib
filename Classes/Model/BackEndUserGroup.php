<?php

declare(strict_types=1);

namespace OliverKlee\Oelib\Model;

use OliverKlee\Oelib\DataStructures\Collection;

/**
 * This class represents a back-end user group.
 *
 * @deprecated will be removed in oelib 6.0
 */
class BackEndUserGroup extends AbstractModel
{
    /**
     * Gets this group's title.
     *
     * @return string the title of this group, will be empty if the group has
     *                none
     */
    public function getTitle(): string
    {
        return $this->getAsString('title');
    }

    /**
     * Returns this group's direct subgroups.
     *
     * @return Collection<BackEndUserGroup> this group's direct subgroups, will be empty if this group has no subgroups
     */
    public function getSubgroups(): Collection
    {
        /** @var Collection<BackEndUserGroup> $groups */
        $groups = $this->getAsCollection('subgroup');

        return $groups;
    }
}
