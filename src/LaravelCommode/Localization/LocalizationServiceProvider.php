<?php
    namespace LaravelCommode\Localization;

    use LaravelCommode\Common\GhostService\GhostService;

    class LocalizationServiceProvider extends GhostService
    {
        public function boot()
        {
            $this->package('laravel-commode/localization');
            $this->commands(['LaravelCommode\Localization\Commands\XMLLang']);
        }

        public function provides()
        {
            return ['commode.localization.registry', 'LaravelCommode\Localization\Interfaces\ILangRegistry'];
        }

        protected function launching() { }

        protected function registering()
        {
            $this->app->singleton(
                'LaravelCommode\Localization\Interfaces\ILangRegistry',
                'LaravelCommode\Localization\LangRegistry\LangRegistry'
            );

            $this->app->bindShared('commode.localization.registry', function()
            {
                return $this->app->make('LaravelCommode\Localization\Interfaces\ILangRegistry');
            });
        }

    }
