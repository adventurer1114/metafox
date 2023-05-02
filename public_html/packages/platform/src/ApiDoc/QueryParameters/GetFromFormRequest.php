<?php

namespace MetaFox\Platform\ApiDoc\QueryParameters;

use Knuckles\Scribe\Extracting\Strategies\GetFromFormRequestBase;
use ReflectionClass;

/**
 * Class GetFromFormRequest.
 * @ignore
 * @codeCoverageIgnore
 */
class GetFromFormRequest extends GetFromFormRequestBase
{
    protected string $customParameterDataMethodName = 'queryParameters';

    /** @var bool */
    protected bool $usesQueryParameters = false;

    protected function isFormRequestMeantForThisStrategy(ReflectionClass $formRequestReflectionClass): bool
    {
        // Only use this FormRequest for query params if there's "Query parameters" in the docblock
        // Or there's a queryParameters() method
        $formRequestDocBlock = $formRequestReflectionClass->getDocComment();

        if (str_contains($formRequestDocBlock, '@usesQueryParameters')) {
            $this->usesQueryParameters = true;

            return true;
        }

        return parent::isFormRequestMeantForThisStrategy($formRequestReflectionClass);
    }
}
