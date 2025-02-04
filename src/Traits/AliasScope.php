<?php

namespace Lib\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AliasScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $maps = method_exists($model, 'fields') ? $model->fields() : [];

        // Modify "where" conditions dynamically
        $builder->getQuery()->where(function ($query) use ($maps) {
            foreach ($query->wheres as &$where) {
                if (isset($where['column']) && array_key_exists($where['column'], $maps)) {
                    $where['column'] = $maps->$where['column'];
                }
            }
        });

        // Modify "orderBy" dynamically
        $builder->getQuery()->orders = array_map(function ($order) use ($maps) {
            if (isset($order['column']) && array_key_exists($order['column'], $maps)) {
                $order['column'] = $maps->$order['column'];
            }
            return $order;
        }, $builder->getQuery()->orders ?? []);

        // Modify "select" dynamically
        $builder->getQuery()->columns = array_map(function ($column) use ($maps) {
            return $maps->$column ?? $column;
        }, $builder->getQuery()->columns ?? ['*']);
    }
}
