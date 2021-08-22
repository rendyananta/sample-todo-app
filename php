[2021-08-22 12:44:49] production.ERROR: No arguments expected for "serve" command, got "artisan". {"exception":"[object] (Symfony\\Component\\Console\\Exception\\RuntimeException(code: 0): No arguments expected for \"serve\" command, got \"artisan\". at /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Input/ArgvInput.php:186)
[stacktrace]
#0 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Input/ArgvInput.php(80): Symfony\\Component\\Console\\Input\\ArgvInput->parseArgument()
#1 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Input/Input.php(55): Symfony\\Component\\Console\\Input\\ArgvInput->parse()
#2 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Command/Command.php(258): Symfony\\Component\\Console\\Input\\Input->bind()
#3 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/laravel/framework/src/Illuminate/Console/Command.php(121): Symfony\\Component\\Console\\Command\\Command->run()
#4 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Application.php(978): Illuminate\\Console\\Command->run()
#5 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Application.php(295): Symfony\\Component\\Console\\Application->doRunCommand()
#6 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/symfony/console/Application.php(167): Symfony\\Component\\Console\\Application->doRun()
#7 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/laravel/framework/src/Illuminate/Console/Application.php(92): Symfony\\Component\\Console\\Application->run()
#8 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(129): Illuminate\\Console\\Application->run()
#9 /home/rendy/Workspace/Kubernetes/laravel-sample-todo/artisan(37): Illuminate\\Foundation\\Console\\Kernel->handle()
#10 {main}
"} 

                                                             
  No arguments expected for "serve" command, got "artisan".  
                                                             

