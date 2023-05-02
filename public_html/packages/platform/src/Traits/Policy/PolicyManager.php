<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Traits\Policy;

use Exception;
use Illuminate\Support\Str;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Support\PolicyRuleInterface;

/**
 * Class PolicyManager.
 */
class PolicyManager
{
    /** @var array<string ,string> */
    private array $policies = [];

    /** @var array<string, mixed> */
    private array $resolvePolicies = [];

    /** @var array<string ,string> */
    private array $policyForModels = [];

    /** @var array<string,string> */
    private array $rules = [];

    /**
     * @var array<string,PolicyRuleInterface>
     */
    private array $resolveRules = [];

    public function __construct()
    {
        if(file_exists($file = base_path('bootstrap/cache/policy_rules.php'))){
            $this->rules = require $file;
        }

        if(file_exists($file = base_path('bootstrap/cache/policy_resources.php'))){
            $this->policies = require $file;
        }
    }

    /**
     * @param array<string,string> $policies
     */
    public function setPolicies(array $policies): void
    {
        $this->policies = $policies;
    }

    /**
     * @param string $model
     * @param string $policy
     */
    public function addPolicy(string $model, string $policy): void
    {
        $this->policies[$model] = $policy;
        $this->policyForModels[$policy] = $model;
    }

    public function addRule(string $model, string $handler): void
    {
        $this->rules[$model] = $handler;
    }

    /**
     * @param string $model
     *
     * @return mixed
     */
    public function getPolicyFor(string $model)
    {
        if (!array_key_exists($model, $this->policies)) {
            return null;
        }

        if (!array_key_exists($model, $this->resolvePolicies)) {
            $this->resolvePolicies[$model] = resolve($this->policies[$model]);
        }

        return $this->resolvePolicies[$model];
    }

    public function getModelFor(string $policy): string
    {
        if (!array_key_exists($policy, $this->policyForModels)) {
            abort(500, "Cannot get model for policy $policy");
        }

        $class = $this->policyForModels[$policy];

        try {
            return $class::ENTITY_TYPE;
        } catch (Exception $e) {
            abort(500, "Cannot get entity type from $class");
        }
    }

    /**
     * @param string $ability
     *
     * @return mixed
     */
    public function getRuleFor(string $ability)
    {
        if (!array_key_exists($ability, $this->resolveRules)) {
            if (!isset($this->rules[$ability])) {
                return null;
            }

            $this->resolveRules[$ability] = resolve($this->rules[$ability]);
        }

        return $this->resolveRules[$ability];
    }

    /**
     * Get all rules.
     *
     * @return string[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get policies.
     *
     * @return array
     */
    public function getPolicies(): array
    {
        return $this->policies;
    }

    /**
     * @param string       $type
     * @param string       $ability
     * @param array<mixed> $arguments
     *
     * @return bool
     */
    public function check(string $type, string $ability, array $arguments): bool
    {
        if (!isset($this->rules[$ability])) {
            return true;
        }

        $handler = $this->getRuleFor($ability);

        /** @var mixed $args */
        $args = [$handler, 'check'];

        if (!is_callable($args)) {
            return false;
        }

        return call_user_func($args, $type, ...$arguments);
    }

    /**
     * @param array<string> $abilities
     * @param mixed         $context
     * @param mixed         $resource
     *
     * @return array<string,bool>
     */
    public function getExtras(array $abilities, $context, $resource): array
    {
        if (!$resource instanceof Content) {
            return [];
        }

        $entityType = $resource->entityType();
        $policy = $this->getPolicyFor($entityType);
        $result = [];

        foreach ($abilities as $ability) {
            $key = 'can_' . Str::snake($ability);
            $result[$key] = $policy->{$ability}($context, $resource);
        }

        return $result;
    }
}
