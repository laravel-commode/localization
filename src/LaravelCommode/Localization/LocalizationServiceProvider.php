<?php
    namespace LaravelCommode\Localization;

    use LaravelCommode\Common\GhostService\GhostService;
    use LaravelCommode\Localization\Commands\XMLLang;

    class LocalizationServiceProvider extends GhostService
    {
        public function boot()
        {
            $this->package('laravel-commode/localization');
            $this->commands([XMLLang::class]);
        }

        public function provides()
        {
            return ['commode.localization.registry', Interfaces\ILangRegistry::class];
        }

        protected function launching() { }

        protected function registering()
        {
            $this->app->singleton(Interfaces\ILangRegistry::class, LangRegistry\LangRegistry::class);

            $this->app->bindShared('commode.localization.registry', function()
            {
                return app(Interfaces\ILangRegistry::class);
            });
        }

    }
