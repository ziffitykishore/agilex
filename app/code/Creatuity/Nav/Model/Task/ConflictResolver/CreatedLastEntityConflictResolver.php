<?php

namespace Creatuity\Nav\Model\Task\ConflictResolver;

class CreatedLastEntityConflictResolver implements EntityConflictResolverInterface
{
    public function resolve(array $entities)
    {
        $entity = end($entities);
        if ($entity !== false) {
            return $entity;
        }

        $entities = var_export($entities, true);
        throw new \Exception("Could not resolve entity from given entities:\n{$entities}\n");
    }
}
