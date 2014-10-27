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

Update your AppKernel.php by using the Kernel class provided by the bundle, and register it

    use Jihel\Plugin\DynamicParameterBundle\HttpKernel\Kernel;

    ...

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Jihel\Plugin\DynamicParameterBundle\JihelPluginDynamicParameterBundle(),
        );
    }

Add to your parameter.yml:

    parameters:
        jihel.plugin.dynamic_parameter.table_prefix: whatever # can be '' for no prefix, will be 'jihel_' by default

Install database table:

    php app/console doctrine:schema:update
    

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


2- Configure cache management
-----------------------------

    parameters.yml
        jihel.plugin.dynamic_parameter.dynamic_parameter_cache: ['env'|true|false]

The 'env' value will use the cache only in production environment
true will enable it anytime.

The keys will be invalidate automatically if you use the controller provided.
Else you will have to use the service `jihel.plugin.dynamic_parameter.manager.cache`
The method 


3- Usage
--------

Add parameters keys to you database.
You can create a crud of your own but a default one is present, to enable it just add the route
to your **routing.yml** file:

    JihelPluginDynamicParameterBundle:
        resource: '@JihelPluginDynamicParameterBundle/Resources/config/routing.yml'
        prefix: /jihel

You will only see the keys visible in your namespace and with the column **isEditable** to true

**/!\ You have to clean the cache when you update the keys directly from database /!\**

To manualy rebuild the cache avec an update, you can


4- Note
-------

Obviously if you load multiple keys with the same name, only one will be registered ...
Beware of the location where you are executing the key add,
you may have to clear the cache manually if you use a back / front app separation.


5 - Important !
---------------

You have to know that the environment parameters in vhost will no longer be cached,
or at least, everywhere but in the service definition (yeah I know ...).

Symfony2 replace the parameter keys by their raw values in cache.
Because of this you can't use dynamic parameters in the dependencie injection system
if you share the source code between project.
A workaround for *environment vars* is provided with the service `jihel.plugin.dynamic_parameter.loader.environment`.

To use them anyway a possible workaround is to inject the service container (yeah I know ... Again ...) directly
in you service instead of only the key.

I'd like to change the way it's done but rewriting the PhpDumper completely may not be a clean solution.
So as usual with those damn private and not protected functions (and you know there is a lot in SF2 ...),
deal with it, inject the container but discard it just after usage like this.

    public function __construct(Container $container)
    {
        $this->myDynamicParameter = $container->getParameter('jihel.dynamic.myDynamic');
    }


6- Thanks
---------

Thanks to me for giving my free time doing class for lazy developers.
You can access read CV [here](http://www.joseph-lemoine.fr)
