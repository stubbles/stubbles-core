3.1.0 (2013-??-??)
------------------

### BC breaks

  * Since 3.0.0 internal encoding of UTF-8 was only enforced within Apps. This has changed again and is not enforced by Stubbles Core any more. If you want to enforce it you need to call `net\stubbles\lang\enforceInternalEncoding()` explicitly.

### Other changes

  * Deprecated `net\stubbles\ioc\App::persistAnnotations()` in favor of `\net\stubbles\lang\persistAnnotations()`, will be removed with 4.0.0
  * Deprecated `net\stubbles\ioc\App::persistAnnotationsInFile()` in favor of `\net\stubbles\lang\persistAnnotationsInFile()`, will be removed with 4.0.0
  * Deprecated `net\stubbles\lang\StringRepresentationBuilder` in favor of `\net\stubbles\lang\__toString()`, will be removed with 4.0.0
  * Introduced new functions in `net\stubbles\lang`
     * `properties()`, parseProperties()` and readPropertyFile()`
     * `reflect()`
     * `persistAnnotations()` and persistAnnotationsInFile()`
     * `enforceInternalEncoding()`
     * `__toString()`
  * Introduced new functions in `net\stubbles\peer`
     * `http()`
     * `headers()`
     * `parseHeaders()`
     * `createSocket()`
     * `createBsdSocket()`


3.0.0 (2013-05-02)
------------------

### BC breaks


  * implemented #58: make annotation cache configurable Please note that annotations are not cached inbetween requests automatically any more. To retain the old behaviour, you need to call `\net\stubbles\lang\reflect\annotation\AnnotationCache::startFromFileCache($projectPath . '/cache/annotations.cache');` In case you have an App instance you can call the new `App::persistAnnotations()` or `App::persistAnnotationsInFile()` method. See also updated documentation about annotation cache at https://github.com/stubbles/stubbles-core/wiki/Annotations#annotation-cache
  * Removed `vendor/bin/clearCache`. Due to changed annotation cache a generic mechanism for clearing the cache can't be provided any longer, applications have to implement their own solution.
  * Removed support for `post-install-cmd` and `post-update-cmd`. You should remove any such entries in your composer.json.
  * Removed package `net\stubbles\cache`, doesn't belong into core
  * Deprecated `net\stubbles\lang\Object` and `net\stubbles\lang\BaseObject`, no core class implements the interface or extends from the base class any more
  * Previously UTF-8 as internal encoding was always enforced, even if only one class was loaded. Now, UTF-8 is only enforced when creating an app instance via `net\stubbles\ioc\App::create()` or `net\stubbles\ioc\App::createInstance()`. If your application doesn't use one of these methods you need to enforce UTF-8 as internal encoding yourself.

### Other changes

  * added `ReflectionClass::fromName()`
  * added `ReflectionObject::fromInstance()`
  * changed status of `net\stubbles\lang\StringRepresentationBuilder` from @internal to @api


2.1.3 (2012-12-28)
------------------

  * changed `net\stubbles\lang\types\datespan\Month` to always use current month, also on first day of month
  * added initial phar support to run stubbles-core from inside a phar
  * added `net\stubbles\lang\ResourceLoader::getRoot()` to allow retrieval of root path in a non-static mockable call
  * fixed notice about array to string conversion in `net\stubbles\lang\StringRepresentationBuilder`


2.1.2 (2012-08-29)
------------------

  * added `net\stubbles\peer\Uri::getQueryString()`
  * fixed line ending bug in `net\stubbles\streams\memory\MemoryInputStream`
  * fixed `net\stubbles\peer\http\HttpRequest` to not ignore query string for GET and HEAD requests
  * fixed bug in `net\stubbles\peer\HeaderList` with recognition of headers that contain a colon in their value


2.1.1 (2012-07-31)
------------------

  * Removed hostname binding with posix, wasn't reliable enough


2.1.0 (2012-07-30)
------------------

  * `net\stubbles\ioc\module\PropertiesBindingModule` can now optionally bind current working directory with name `net.stubbles.cwd`
  * `net\stubbles\ioc\module\PropertiesBindingModule` can now optionaly bind current hostname, using `net.stubbles.hostname.nq` for non-qualified and `net.stubbles.hostname.fq` for qualified hostname
  * implemented issue #33: allow adhoc binding module using closures
  * implemented issue #31: allow binding to closures


2.0.0 (2012-05-22)
------------------

  * Initial release.
