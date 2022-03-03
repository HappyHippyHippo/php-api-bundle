<?php

namespace Hippy\Api\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/** @codeCoverageIgnore */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hippy_api');
        $treeBuilder->getRootNode()
            ->children()
                ->append($this->addAccess())
                ->append($this->addApp())
                ->append($this->addCors())
                ->append($this->addEndpoint())
                ->append($this->addErrors())
                ->append($this->addLog())
                ->append($this->addVersion())
            ->end();

        return $treeBuilder;
    }

    private function addAccessAllowList(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('allow');
        return $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('globals')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('endpoints')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addAccessDenyList(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('deny');
        return $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('globals')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('endpoints')
                    ->arrayPrototype()
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addAccess(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('access');
        return $treeBuilder->getRootNode()
            ->children()
                ->append($this->addAccessAllowList())
                ->append($this->addAccessDenyList())
            ->end();
    }

    private function addApp(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('app');
        return $treeBuilder->getRootNode()
            ->children()
                ->integerNode('id')->isRequired()->end()
                ->scalarNode('name')->isRequired()->end()
                ->scalarNode('version')->defaultValue('development')->end()
            ->end();
    }

    private function addEndpointConfig(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('config');
        return $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
            ->end();
    }

    private function addEndpointOpenApi(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('openapi');
        return $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->scalarNode('source')->defaultValue('/openapi/openapi.yaml')->end()
                ->arrayNode('servers')
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }

    private function addEndpoint(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('endpoint');
        return $treeBuilder->getRootNode()
            ->children()
                ->append($this->addEndpointConfig())
                ->append($this->addEndpointOpenApi())
            ->end();
    }

    private function addCors(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('cors');
        return $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->scalarNode('origin')->defaultValue('*')->end()
            ->end();
    }

    private function addErrors(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('errors');
        return $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('trace')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();
    }

    private function addLoggingElement(string $name, string $default = 'info'): NodeDefinition
    {
        $treeBuilder = new TreeBuilder($name);
        return $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('state')->defaultTrue()->end()
                ->scalarNode('message')->defaultValue($name)->end()
                ->enumNode('level')
                    ->defaultValue($default)
                    ->values([
                        'emergency',
                        'alert',
                        'critical',
                        'error',
                        'warning',
                        'notice',
                        'info',
                        'debug'
                    ])
                ->end()
            ->end();
    }

    private function addLog(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('log');
        return $treeBuilder->getRootNode()
            ->children()
                ->append($this->addLoggingElement('request'))
                ->append($this->addLoggingElement('response'))
                ->append($this->addLoggingElement('exception', 'warning'))
            ->end();
    }

    private function addVersion(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('version');
        return $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('header')
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end();
    }
}
