[![Build Status](https://travis-ci.org/brainexe/core.svg?branch=master)](https://travis-ci.org/brainexe/core)
[![Latest Stable Version](https://poser.pugx.org/brainexe/core/v/stable)](https://packagist.org/packages/brainexe/core) [![Total Downloads](https://poser.pugx.org/brainexe/core/downloads)](https://packagist.org/packages/brainexe/core) [![Latest Unstable Version](https://poser.pugx.org/brainexe/core/v/unstable)](https://packagist.org/packages/brainexe/core) [![License](https://poser.pugx.org/brainexe/core/license)](https://packagist.org/packages/brainexe/core)
[![Code Coverage](https://scrutinizer-ci.com/g/brainexe/core/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brainexe/core/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brainexe/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brainexe/core/?branch=master)

# Features

##ServiceAnnotations
 - DIC (@Annotaion bases)
   - @Service for usual services 
   - @Command to register Symfony Commands
   - @Controller (using @Route and @Guest method annotation)
   - @EventListener
   - @Middleware
 - Redis database
 - Background Events via Message Queue
 - Request middlewares
   - Authentication (set current user)
   - Locale
   - Cache
   - Generation time log
   - ...
 - Input control (define commands via regexp. e.g. "send mail to .*? with subject '.*?' and body '.*?'")
 - Arduino API
