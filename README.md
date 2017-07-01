Yii 2 RESTful Project Template
===============================

This is a highly specialized Yii2 Template, which is focused mainly on RESTful API services development.

Be careful, this template has no frontend nor control panel so if you want those sections built-in go for the [yii2-standard template](https://github.com/ByteHunter/yii2-standard) or alternatively find it in Packagist: [bytehunter/yii2-standard](https://packagist.org/packages/bytehunter/yii2-standard)


DIRECTORY STRUCTURE
-------------------

```
api
    common/              contains common
    config/              contains api configurations
    modules/             contains api versions separated by modules
        v1/              contains a major api version
            controllers/ contains version specific api controllers
            models/      contains version specific api models
    runtime/             contains files generated during runtime
    web/                 contains the entry script
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both api and console
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
