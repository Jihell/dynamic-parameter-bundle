DynamicParameterBundle
======================

Symfony 2 allow you to access your parameters in a file, but what if you need it from a database ?
This bundle will create all keys before any other load.

You can also load a bunch of parameters from a specific namespace from an apache vhost environment var


1- Install
----------

Add plugin to your composer.json require:

    {
        "require": {
            "jihel/dynamic-parameter-bundle": "dev-master",
        }
    }

or

    php composer.phar require jihel/dynamic-parameter-bundle

Add bundle to AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Jihel\Plugin\DynamicParameterBundle\JihelPluginDynamicParameterBundle(),
        );
    }

### Optional

You can define some namespaces keys to load from you vhost configuration.

The is two keys:
- SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__ALLOW_NAMESPACES
- SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__DENY_NAMESPACES

You can specify multiple namespaces with a comma separator.
The request is associative as you can specify both allowed and denied namespaces.

Exemple:
    
    <VirtualHost *:80>
        ServerName      Symfony2
        DocumentRoot    "/path/to/symfony_2_app/web"
        DirectoryIndex  index.php index.html
        SetEnv          SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__ALLOWED_NAMESPACES firstModule
        SetEnv          SYMFONY__JIHEL__PLUGIN__DYNAMIC_PARAMETER__DENIED_NAMESPACES firstOption,secondOption
    
        <Directory "/path/to/symfony_2_app/web">
            AllowOverride All
            Allow from All
        </Directory>
    </VirtualHost>


2- Configure your config.yml
----------------------------

Add bundle to your config.yml

    imports:
        - { resource: database.php }
        - { resource: ../../vendor/jihel/dynamic-parameter-bundle/src/parameters.php }
        - { resource: parameters.yml }
        ...

As the database configuration is not already loaded, you have to use an external file like
the one provided by the bundle.
copy the file `database.yml.dist` to a safe location and rename it to `database.yml`

Replace the placeholder with your parameters for keys

- `database_driver`
- `database_host`
- `database_port`
- `database_name`
- `database_user`
- `database_password`
- `jihel.plugin.dynamic_parameter.dynamic_parameter_cache`: Will save in cache the parameters from database. accepted values ['env'|true|false]
- `jihel.plugin.dynamic_parameter.apache_parameter_cache`: Will save in cache the parameters from vhost. accepted values ['env'|true|false]
- `jihel.plugin.dynamic_parameter.table_prefix`: You can change the table prefix by what you want

The 'env' value will use the cache only in production environment

You don't have to define it again in the usual parameter.yml,
except if you want to overload the dynamic configuration


3- Usage
--------

Add parameters keys to you database
You can create a crud of your own but a default one is present, to enable it just add the route
to yout **routing.yml** file:

    JihelPluginDynamicParameterBundle:
        resource: '@JihelPluginDynamicParameterBundle/Resources/config/routing.yml'
        prefix: /jihel

You will only see the keys visible in your namespace and with the column **isEditable** to true

**/!\ You have te clean the cache when you update the keys /!\**


4- Note
-------

Obviously if you load multiple keys with the same name, only one will be registered ...
Beware of the location where you are executing the key add,
you may have to clear the cache manually if you use a back / front app separation.


5- Thanks
---------

Thanks to me for giving my free time doing class for lazy developers.
You can access read CV [here](http://www.joseph-lemoine.fr)
