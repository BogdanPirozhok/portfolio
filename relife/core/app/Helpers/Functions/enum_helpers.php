<?php

/**
 * @param int $id
 * @param array $entity
 * @return array
 */
function enum_find(array $entity, int $id): array
{
    return collect($entity)->firstWhere('id', $id);
}
