[![Build Status](https://travis-ci.org/brainexe/core.svg?branch=master)](https://travis-ci.org/brainexe/core)
[![Code Coverage](https://scrutinizer-ci.com/g/brainexe/core/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/brainexe/core/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brainexe/core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brainexe/core/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5669f0b943cfea00320002b0/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5669f0b943cfea00320002b0)

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
