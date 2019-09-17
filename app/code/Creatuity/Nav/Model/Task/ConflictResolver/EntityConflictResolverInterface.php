<?php

namespace Creatuity\Nav\Model\Task\ConflictResolver;

interface EntityConflictResolverInterface
{
    public function resolve(array $entities);
}
