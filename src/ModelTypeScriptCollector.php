<?php

namespace Nolanos\LaravelModelTypescriptTransformer;

use ReflectionClass;

use Illuminate\Database\Eloquent\Model;
use Spatie\TypeScriptTransformer\Collectors\Collector;
use Spatie\TypeScriptTransformer\Structures\TransformedType;

class ModelTypeScriptCollector extends Collector
{
    public function getTransformedType(ReflectionClass $class): TransformedType|null
    {
        if (! $class->isSubclassOf(Model::class)) {
            return null;
        }

        $transformer = new ModelTransformer;

        return $transformer->transform($class, $class->getShortName());
    }
}
