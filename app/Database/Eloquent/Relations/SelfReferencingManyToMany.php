<?php

/**
 * https://stackoverflow.com/a/55145000/5749974
 */

namespace App\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SelfReferencingManyToMany extends BelongsToMany
{
    /**
     * Get a new pivot statement for a given "other" ID.
     *
     * @param  mixed  $id
     * @return \Illuminate\Database\Query\Builder
     */
    public function newPivotStatementForId($id)
    {
        $ids = $this->parseIds($id);

        return $this->newPivotQuery()->whereIn($this->relatedPivotKey, $ids)
            ->orWhereIn($this->foreignPivotKey, $ids);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*'])
    {
        // duplicated from "BelongsToMany"
        $builder = $this->query->applyScopes();

        $columns = $builder->getQuery()->columns ? [] : $columns;

        // Adjustments for "Many to Many on self": do not get the resulting models here directly, but rather
        // just set the columns to select and do some adjustments to also select the "inverse" records
        $builder->addSelect(
            $this->shouldSelect($columns)
        );

        // backup order directives
        $orders = $builder->getQuery()->orders;
        $builder->getQuery()->orders = [];

        // clone the original query
        $query2 = clone($this->query);

        // determine the columns to select - same as in original query, but with inverted pivot key names
        $query2->select(
            $this->shouldSelectInverse( $columns )
        );
        // remove the inner join and build a new one, this time using the "foreign" pivot key
        $query2->getQuery()->joins = array();

        $baseTable = $this->related->getTable();
        $key = $baseTable.'.'.$this->relatedKey;
        $query2->join($this->table, $key, '=', $this->getQualifiedForeignPivotKeyName());

        // go through all where conditions and "invert" the one relevant for the inner join
        foreach( $query2->getQuery()->wheres as &$where ) {
            if(
                $where['type'] == 'Basic'
                && $where['column'] == $this->getQualifiedForeignPivotKeyName()
                && $where['operator'] == '='
                && $where['value'] == $this->parent->{$this->parentKey}
            ) {
                $where['column'] = $this->getQualifiedRelatedPivotKeyName();
                break;
            }
        }

        // add the duplicated and modified and adjusted query to the original query with union
        $builder->getQuery()->union($query2);

        // reapply orderings so that they are used for the "union" rather than just the individual queries
        if (! is_null($orders)) {
            foreach($orders as $ord) {
                $builder->getQuery()->orderBy($ord['column'], $ord['direction']);
            }
        }
        
        // back to "normal" - get the models
        $models = $builder->getModels();
        $this->hydratePivotRelation($models);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }


    /**
     * Get the select columns for the relation query.
     *
     * @param  array  $columns
     * @return array
     */
    protected function shouldSelectInverse(array $columns = ['*'])
    {
        if ($columns == ['*']) {
            $columns = [$this->related->getTable().'.*'];
        }

        return array_merge($columns, $this->aliasedPivotColumnsInverse());
    }

    /**
     * Get the pivot columns for the relation.
     *
     * "pivot_" is prefixed ot each column for easy removal later.
     *
     * @return array
     */
    protected function aliasedPivotColumnsInverse()
    {
        $collection = collect( $this->pivotColumns )->map(function ($column) {
            return $this->table.'.'.$column.' as pivot_'.$column;
        });
        $collection->prepend(
            $this->table.'.'.$this->relatedPivotKey.' as pivot_'.$this->foreignPivotKey
        );
        $collection->prepend(
            $this->table.'.'.$this->foreignPivotKey.' as pivot_'.$this->relatedPivotKey
        );

        return $collection->unique()->all();
    }

}