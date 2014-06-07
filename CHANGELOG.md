4.0.0 (2014-06-??)
------------------

### BC breaks

  * removed namespace prefix `net`, base namespace is now `stubbles` only
  * removed the following classes, can now be found in separate package stubbles/date:
    * `net\stubbles\lang\types\Date`
    * `net\stubbles\lang\types\DateModifier`
    * `net\stubbles\lang\types\TimeZone`
    * `net\stubbles\lang\types\datespan\AbstractDatespan`
    * `net\stubbles\lang\types\datespan\CustomDatespan`
    * `net\stubbles\lang\types\datespan\Datespan`
    * `net\stubbles\lang\types\datespan\Day`
    * `net\stubbles\lang\types\datespan\Month`
    * `net\stubbles\lang\types\datespan\Week`
    * `net\stubbles\lang\types\datespan\Year`
  * removed `net\stubbles\lang\Clonable`
  * removed `net\stubbles\lang\types\LocalizedString`
  * removed `net\stubbles\lang\enforceInternalEncoding()`, not supported since PHP 5.6 any more
  * removed `net\stubbles\ioc\App::createPropertiesBindingModule()`, deprecated since 3.4.0
  * removed `net\stubbles\ioc\module\PropertiesBindingModule`, deprecated since 3.4.0
  * removed `net\stubbles\ioc\App::persistAnnotations()`, deprecated since 3.1.0
  * removed `net\stubbles\ioc\App::persistAnnotationsInFile()`, deprecated since 3.1.0
  * removed `net\stubbles\lang\StringRepresentationBuilder`, deprecated since 3.1.0
  * removed `net\stubbles\lang\Object` and `net\stubbles\lang\BaseObject`, deprecated since 3.0.0
  * all properties in `stubbles\lang\Properties` with key `password` are automatically stored as `stubbles\lang\SecureString`
  * major API rework
    * deprecated `stubbles\lang\Properties::getSections()`, iterate over instance instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\Properties::hasSection()`, use `stubbles\lang\Properties::containSection()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\Properties::getSection()`, use `stubbles\lang\Properties::section()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\Properties::getSectionKeys()`, use `stubbles\lang\Properties::keysForSection()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\Properties::hasValue()`, use `stubbles\lang\Properties::containValue()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\Properties::getValue()`, use `stubbles\lang\Properties::value()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\ResourceLoader::getProjectResourceUri()´, use `stubbles\lang\ResourceLoader::open()` or `stubbles\lang\ResourceLoader::load()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\ResourceLoader::getResourceUris()´, use `stubbles\lang\ResourceLoader::availableResourceUris()` instead, will be removed with 5.0.0
    * deprecated `stubbles\lang\ResourceLoader::getRootPath()´ and `stubbles\lang\ResourceLoader::getRoot()´, use `stubbles\lang\Rootpath` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\AcceptHeader::getList()`, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getScheme()`, use `stubbles\peer\Uri::scheme()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getUser()`, use `stubbles\peer\Uri::user()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getPassword()`, use `stubbles\peer\Uri::password()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getHost()`, use `stubbles\peer\Uri::hostname()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getPort()`, use `stubbles\peer\Uri::port()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getPath()`, use `stubbles\peer\Uri::path()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getQueryString()`, use `stubbles\peer\Uri::queryString()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getParam()`, use `stubbles\peer\Uri::param()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\Uri::getFragment()`, use `stubbles\peer\Uri::fragment()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\Http::getStatusClass()`, use `stubbles\peer\http\Http::statusClassFor()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\Http::getStatusCodes()`, use `stubbles\peer\http\Http::statusCodes()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\Http::getReasonPhrase()`, use `stubbles\peer\http\Http::reasonPhraseFor()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getStatusLine()`, use `stubbles\peer\http\HttpResponse::statusLine()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getHttpVersion()`, use `stubbles\peer\http\HttpResponse::httpVersion()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getStatusCode()`, use `stubbles\peer\http\HttpResponse::statusCode()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getStatusCodeClass()`, use `stubbles\peer\http\HttpResponse::statusCodeClass()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getReasonPhrase()`, use `stubbles\peer\http\HttpResponse::reasonPhrase()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getHeader()`, use `stubbles\peer\http\HttpResponse::headers()` instead, will be removed with 5.0.0
    * deprecated `stubbles\peer\http\HttpResponse::getBody()`, use `stubbles\peer\http\HttpResponse::body()` instead, will be removed with 5.0.0


### Other changes

  * `stubbles\lang\ResourceLoader` now supports PSR-4
  * fixed bug: `stubbles\lang\ModifiableProperties::merge()` now returns `stubbles\lang\ModifiableProperties::merge()` instead of `stubbles\lang\Properties::merge()` only
  * added `stubbles\lang\ModifiableProperties::unmodifiable()`
  * added `stubbles\lang\Rootpath`
  * added `stubbles\lang\SecureString`


3.5.3 (2014-05-07)
------------------

   * fixed PHP error when calling `net\stubbles\lang\types\datespan\Month::fromString()` with invalid value


3.5.2 (2014-05-07)
------------------

   * added `net\stubbles\lang\types\datespan\Month::fromString()` to create an instance from a string like 2014-05


3.5.1 (2014-05-07)
------------------

   * added `net\stubbles\lang\types\datespan\Month::last()` to create an instance for the previous month
   * added `net\stubbles\lang\types\datespan\Day::tomorrow()` and `net\stubbles\lang\types\datespan\Day::yesterday()` to create an instance for tomorrow or yesterday


3.5.0 (2014-05-06)
------------------

   * raised minimum PHP version to 5.4.0
   * the following methods now except anything that can be casted to an instance of `net\stubbles\lang\types\Date` via `net\stubbles\lang\types\Date::castFrom()`
     * `net\stubbles\lang\types\Date::isBefore()`
     * `net\stubbles\lang\types\Date::isAfter()`
     * `net\stubbles\lang\types\TimeZone::getOffset()`
     * `net\stubbles\lang\types\TimeZone::getOffsetInSeconds()`
     * `net\stubbles\lang\types\TimeZone::translate()`
     * `net\stubbles\lang\types\datespan\CustomDatespan::__construct()`
     * `net\stubbles\lang\types\datespan\Day::__construct()`
     * `net\stubbles\lang\types\datespan\Week::__construct()`
     * `net\stubbles\lang\types\datespan\Datespan::containsDate()`
   * added several shortcut methods on `net\stubbles\lang\types\datespan\Datespan`:
     * `startsBefore($date)`
     * `startsAfter($date)`
     * `endsBefore($date)`
     * `endsAfter($date)`
     * `formatStart($format, TimeZone $timeZone = null)`
     * `formatEnd($format, TimeZone $timeZone = null)`


3.4.4 (2014-04-11)
------------------

   * added `net\stubbles\lang\types\Date::castFrom()`


3.4.3 (2014-03-25)
------------------

   * fixed `net\stubbles\ioc\App::createModeBindingModule()` not accepting `$projectPath`


3.4.2 (2014-03-25)
------------------

   * added `net\stubbles\lang\exception\lastErrorMessage()`


3.4.1 (2014-01-22)
------------------

   * fixed bug: project path for `net\stubbles\lang\errorhandler\ExcepionLogger` erronously marked with `@Property`


3.4.0 (2014-01-21)
------------------

### BC breaks

   * Deprecated `net\stubbles\ioc\App::createPropertiesBindingModule()`
   * Properties are now bound via `net\stubbles\ioc\App::createModeBindingModule()`
   * Current working directory can now be bound via `net\stubbles\ioc\App::bindCurrentWorkingDirectory()`
   * Current hostnames can now be bound via `net\stubbles\ioc\App::bindHostname()`

### Other changes

   * implemented issue #77: properties with values depending on runtime mode


3.3.1 (2014-01-13)
------------------

   * added '@Inject' to `net\stubbles\lang\errorhandler\ExceptionLogger`


3.3.0 (2014-01-12)
------------------

   * added `net\stubbles\lang\errorhandler\ExceptionLogger`


3.2.0 (2013-10-29)
------------------

   * added `net\stubbles\streams\OutputStream::writeLines()` and to all provided implementations


3.1.2 (2013-09-17)
------------------

   * added `net\stubbles\lang\ResourceLoader::getProjectResourceUri()`


3.1.1 (2013-09-11)
------------------

   * added `net\stubbles\lang\reflect\ReflectionParameter::getType()`
   * added `net\stubbles\lang\typeFor()`
   * added `net\stubbles\lang\reflect\MixedType`
   * added `net\stubbles\lang\reflect\ReflectionPrimitive::isKnown()`
   * added support for `void`, `mixed` and `object` in `net\stubbles\lang\reflect\ReflectionMethod` and `net\stubbles\lang\reflect\ReflectionFunction`
   * fixed error when composer vendor pathes were arrays, not just a string


3.1.0 (2013-05-05)
------------------

### BC breaks

  * Since 3.0.0 internal encoding of UTF-8 was only enforced within Apps. This has changed again and is not enforced by Stubbles Core any more. If you want to enforce it you need to call `net\stubbles\lang\enforceInternalEncoding()` explicitly.
  * Due to removal of the default annotation cache and the `net\stubbles\cache` with 3.0.0 the `net\stubbles\ioc\module\PropertiesBindingModule` doesn't bind the `net.stubbles.cache.path` any more by default. In case you still require this path you need to explicitly enable it via  `net\stubbles\ioc\module\PropertiesBindingModule::addPathType('cache')`, see https://github.com/stubbles/stubbles-core/wiki/Apps-properties#pathes-as-properties

### Other changes

  * Deprecated `net\stubbles\ioc\App::persistAnnotations()` in favor of `\net\stubbles\lang\persistAnnotations()`, will be removed with 4.0.0
  * Deprecated `net\stubbles\ioc\App::persistAnnotationsInFile()` in favor of `\net\stubbles\lang\persistAnnotationsInFile()`, will be removed with 4.0.0
  * Deprecated `net\stubbles\lang\StringRepresentationBuilder` in favor of `\net\stubbles\lang\__toString()`, will be removed with 4.0.0
  * Introduced new functions in `net\stubbles\lang`
     * `properties()`, `parseProperties()` and `parsePropertiesFile()`
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
