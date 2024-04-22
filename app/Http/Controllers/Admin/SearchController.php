<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Relationship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Perform a global search.
     */
    public function search(Request $request): JsonResponse
    {
        $results = collect();
        $search = $request->search;
        $limit = $request->limit ?? 25;
        $params = $request->params ?? [];

        if ($request->models) {
            foreach ($request->models as $model) {
                $class = Relation::getMorphedModel($model);

                $query = $class::where(function (Builder $query) use ($class, $search) {
                    $isFirst = true;

                    foreach ($class::getSearchableAttributes() as $attribute) {
                        $whereClause = $isFirst ? 'where' : 'orWhere';

                        $split = explode('.', $attribute);

                        if (count($split) > 1) {
                            $relation = $split[0];
                            $attribute = $split[1];

                            $whereClause .= 'Has';

                            $query->{$whereClause}($relation, function (Builder $query) use ($attribute, $search) {
                                $query->where($attribute, 'like', "%{$search}%");
                            });
                        } else {
                            $query->{$whereClause}($attribute, 'like', "%{$search}%");
                        }

                        $isFirst = false;
                    }

                    $query->orWhere('id', $search);

                    return $query;
                });

                foreach ($params as $key => $param) {
                    if (is_array($param)) {
                        $query->where($key, $param['operator'], $param['value']);
                    } else {
                        $query->where($key, $param);
                    }
                }

                if ($request->exclude) {
                    $query->whereNotIn('id', $request->exclude);
                }

                if (in_array($model, ['event', 'activity', 'group'])) {
                    $query->with('season');
                }

                $modelResults = $query->limit($limit)->get();

                if ($modelResults->count() > 0) {
                    $results->push(
                        $modelResults->map(function ($result) use ($model) {
                            $result->model = $model;

                            return $result;
                        })
                    );
                }
            }

            return response()->json(
                $results->flatten(1)
                    ->transform(fn ($item) => [
                        'id' => $item->id,
                        'title' => $item->title,
                        'model' => $item->model,
                        'season' => $item->season ? $item->season->title : null,
                    ])
                    ->sortBy('title')
                    ->values()
                    ->all()
            );
        }

        return response()->json([]);
    }

    /**
     * Resolve relationships.
     */
    public function resolve(Request $request): mixed
    {
        if ($request->id) {
            return Relationship::resolve($request->id, $request->model, ['id', 'title', 'model']);
        }

        return response()->json(null);
    }
}
