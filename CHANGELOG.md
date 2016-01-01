7.0.0 (201?-??-??)
------------------

### BC breaks

  * raised minimum required PHP version to 5.6
  * changed `stubbles\ioc\module\BindingModule::configure()` to accept an optional second parameter `$projectPath`
  * deprecated `stubbles\lang\SecureString` in favor of `stubbles\lang\Secret`, will be removed in 8.0.0
  * removed `stubbles\peer\createBsdSocket()`, `stubbles\peer\BsdSocket` was already removed in 6.0.0
  * `stubbles\peer\http\Http::lines()` no longer accepts an array, but an arbitrary amount of strings instead
  * deprecated `stubbles\peer\HeaderList::size()`, use `stubbles\peer\HeaderList::count()` or `count($headerlist)` instead, will be removed with 8.0.0
  * deprecated `stubbles\predicate\Predicate::asWellAs()` in favor of `stubbles\predicate\Predicate::and()`, will be removed with 8.0.0
  * deprecated `stubbles\predicate\Predicate::orElse()` in favor of `stubbles\predicate\Predicate::or()`, will be removed with 8.0.0
  * deprecated `stubbles\ioc\App`, use `stubbles\App` instead, will be removed with 8.0.0
  * deprecated `stubbles\lang\Mode` use `stubbles\Environment` instead, will be removed with 8.0.0
  * deprecated `stubbles\lang\DefaultMode::prod()` use `stubbles\environments\Production` instead, will be removed with 8.0.0
  * deprecated `stubbles\lang\DefaultMode::dev()` use `stubbles\environments\Development` instead, will be removed with 8.0.0
  * deprecated `stubbles\lang\DefaultMode`, will be removed with 8.0.0
  * moved `stubbles\ioc\modules\Runtime` to `stubbles\Runtime`
  * deprecated `stubbles\lang\errorhandler\CompositeErrorHandler`, will be removed with 8.0.0
  * deprecated `stubbles\lang\errorhandler\ErrorHandler`, will be removed with 8.0.0
  * deprecated `stubbles\lang\errorhandler\ExceptionHandler`, will be removed with 8.0.0
  * deprecated `stubbles\lang\errorhandler\ExceptionLogger`, use `stubbles\environments\ExceptionLogger` instead, will be removed with 8.0.0
  * moved `stubbles\lang\ResourceLoader` to `stubbles\ResourceLoader`, old class definition will be removed with 8.0.0
  * moved `stubbles\lang\Result` to `stubbles\Result`, old class definition will be removed with 8.0.0
  * moved `stubbles\lang\Rootpath` to `stubbles\Rootpath`, old class definition will be removed with 8.0.0
  * deprecated `stubbles\lang\exception\ConfigurationException` will be removed with 8.0.0
  * `stubbles\lang\Properties::fromFile()` now throws a `\UnexpectedValueException` instead of `stubbles\lang\exception\IOException`


### Other changes

  * added `stubbles\predicate\Predicate::and()`
  * added `stubbles\predicate\Predicate::or()`
  * added `stubbles\ioc\Binder::createInjector(callable ...$applyBindings)`
  * removed seeking restrictions on `stubbles\streams\StandardInputStream`
  * fixed `stubbles\lang\iterator\MappingIterator` calling value- and key-mapper when end of iteration reached


6.3.0 (2015-07-01)
------------------

  * allow access to project path in `stubbles\ioc\module\Runtime`


6.2.0 (2015-06-19)
------------------

  * added `stubbles\streams\nonEmptyLinesOf()`
  * added new methods to `stubbles\lang\Result`
    * `stubbles\lang\Result::isEmpty()`
    * `stubbles\lang\Result::whenEmpty()`
    * `stubbles\lang\Result::applyWhenEmpty()`


6.1.0 (2015-05-31)
------------------

### BC breaks

  * `stubbles\lang\Result::whenNull()` and `stubbles\lang\Result::applyWhenNull()` now return an instance of `stubbles\lang\Result` instead if the raw value to allow more chaining


6.0.0 (2015-05-28)
------------------

### BC breaks

  * `stubbles\ioc` does not support setter injection any more, deprecated since 5.1.0
  * removed `stubbles\ioc\Binder::setSessionScope()`, deprecated since 5.4.0
  * removed old reflection, deprecated since 5.3.0:
    * `stubbles\lang\reflect\BaseReflectionClass`
    * `stubbles\lang\reflect\MixedType`
    * `stubbles\lang\reflect\ReflectionClass`
    * `stubbles\lang\reflect\ReflectionExtension`
    * `stubbles\lang\reflect\ReflectionFunction`
    * `stubbles\lang\reflect\ReflectionMethod`
    * `stubbles\lang\reflect\ReflectionObject`
    * `stubbles\lang\reflect\ReflectionParameter`
    * `stubbles\lang\reflect\ReflectionPrimitive`
    * `stubbles\lang\reflect\ReflectionProperty`
    * `stubbles\lang\reflect\ReflectionRoutine`
    * `stubbles\lang\reflect\ReflectionType`
    * `stubbles\lang\reflect\matcher\MethodMatcher`
    * `stubbles\lang\reflect\matcher\PropertyMatcher`
    * `stubbles\lang\typeFor()`
    * `stubbles\lang\reflect\annotation\Annotatable`
    * `stubbles\lang\reflect\annotation\Annotations::of()`
    * `stubbles\lang\reflect()` now returns PHP's internal reflection classes
    * `Parse::toClass()` now returns `\ReflectionClass`
  * removed `stubbles\ioc\App::bindCurrentWorkingDirectory()`, use `stubbles\ioc\App::currentWorkingDirectory()` instead, deprecated since 5.0.0
  * removed `stubbles\ioc\App::bindHostname()`, use `stubbles\ioc\App::hostname()` instead, deprecated since 5.0.0
  * removed several exceptions where build-in exceptions in PHP exist, deprecated since 5.0.0:
    * `stubbles\lang\exception\IllegalAccessException`, use `LogicException` instead
    * `stubbles\lang\exception\IllegalArgumentException`, use `InvalidArgumentException` instead
    * `stubbles\lang\exception\IllegalStateException`, use `LogicException` instead
    * `stubbles\lang\exception\MethodInvocationException`, use `BadMethodCallException` instead
    * `stubbles\lang\exception\MethodNotSupportedException`, use `BadMethodCallException` instead
    * `stubbles\lang\exception\RuntimeException`, use native `RuntimeException` instead
  * removed `stubbles\lang\exception\Throwable`, deprecated since 5.0.0
  * split `stubbles\peer\Socket` into two classes
    * `stubbles\peer\Socket::connect()` now returns an instance of `stubbles\peer\Stream`
    * all functionallity to read and write on a socket is now in `stubbles\peer\Stream`
  * removed `stubbles\peer\BsdSocket`
  * `stubbles\peer\IpAddress::openSocket()` and `stubbles\peer\IpAddress::openSecureSocket()` now return `stubbles\peer\Stream`


### Other changes

  * added `stubbles\lang\Result`
  * `@ImplementedBy` can now be speficied per runtime mode
  * it is not required any more to mark constructors with `@Inject` when they should be subject to dependency injection
  * added `stubbles\peer\IpAddress::createSocket()` and `stubbles\peer\IpAddress::createSecureSocket()`
  * improved speed for `stubbles\peer\http\HttpUri::hasDnsRecord()` by checking only relevant DNS records
  * sequences returned from the following methods now have the name of the method/property/parameter as key:
    * `stubbles\lang\reflect\methodsOf()`
    * `stubbles\lang\reflect\propertiesOf()`
    * `stubbles\lang\reflect\parametersOf()`
    * `stubbles\lang\reflect\parametersOfConstructor()`
    * `stubbles\lang\reflect\parameter()`
    * `stubbles\lang\reflect\constructorParameter()`


5.5.1 (2015-05-12)
------------------

  * fixed annotation string values which contained both ' and "


5.5.0 (2015-05-06)
------------------

  * added `stubbles\peer\Uri::withPath()`


5.4.1 (2015-04-22)
------------------

  * allowed iteration with non-seekable input streams


5.4.0 (2015-04-01)
------------------

### BC breaks

  * Enabled session scoped bindings even when no session exists. However, retrieving a session scoped instance without setting a session will throw a `\RuntimeException`
  * deprecated `stubbles\ioc\Binder::setSessionScope()`, use built-in session scope with session interface instead, will be removed with 6.0.0
  * added `stubbles\ioc\binding\Session` as simple session interface for the built-in session scope
  * added `stubbles\ioc\Injector::setSession()` to provide session instance for session binding scope, will also bind session interface to this instance so it is available for injection


### Other changes

  * added `stubbles\streams\StandardInputStream`
  * added `stubbles\streams\StandardOutputStream`
  * annotated `stubbles\lang\errorhandler\ExceptionLogger` with `@Singleton`
  * added `stubbles\lang\castToArray()`
  * `stubbles\lang\Sequence::append()` now accepts anything


5.3.2 (2015-03-09)
------------------

  * ensured `stubbles\lang\Sequence` is serialized to XML in a proper way with older versions of stubbles/xml
  * ensured `stubbles\lang\Sequence` can be serialized as JSON


5.3.1 (2015-03-09)
------------------

  * `stubbles\peer\Uri::addParam()` now accepts objects with `__toString()` method


5.3.0 (2015-03-05)
------------------

### BC breaks

  * deprecated classes in `stubbles\lang\reflect`, use PHP's native reflection instead, will be removed with 6.0.0
    * `stubbles\lang\reflect\BaseReflectionClass`
    * `stubbles\lang\reflect\MixedType`
    * `stubbles\lang\reflect\ReflectionClass`
    * `stubbles\lang\reflect\ReflectionExtension`
    * `stubbles\lang\reflect\ReflectionFunction`
    * `stubbles\lang\reflect\ReflectionMethod`
    * `stubbles\lang\reflect\ReflectionObject`
    * `stubbles\lang\reflect\ReflectionParameter`
    * `stubbles\lang\reflect\ReflectionPrimitive`
    * `stubbles\lang\reflect\ReflectionProperty`
    * `stubbles\lang\reflect\ReflectionRoutine`
    * `stubbles\lang\reflect\ReflectionType`
    * `stubbles\lang\reflect\matcher\MethodMatcher`
    * `stubbles\lang\reflect\matcher\PropertyMatcher`
  * added `stubbles\lang\reflect\annotationsOf()` which allows to retrieve annotations without using the `stubbles\lang\reflect\Reflection*` classes
  * added `stubbles\lang\reflect\annotationsOfConstructor()` as shortcut
  * added `stubbles\lang\reflect\annotationsOfParameter()` as shortcut
  * added `stubbles\lang\reflect\annotationsOfConstructorParameter()` as shortcut
  * all typehints in ˚\stubbles\ioc` classes which where against `stubbles\lang\reflect\Reflection*` classes now use PHP standard reflection classes
  * deprecated `stubbles\lang\typeFor()`, will be removed with 6.0.0
  * deprecated `stubbles\lang\reflect\annotation\Annotations::of()`, use `stubbles\lang\reflect\annotation\Annotations::named()` instead, will be removed with 6.0.0


### Other changes

  * added `stubbles\lang\reflect\annotation\Annotations::firstNamed()`
  * added `stubbles\lang\reflect\methodsOf()`
  * added `stubbles\lang\reflect\propertiesOf()`
  * added `stubbles\lang\reflect\parametersOf()`
  * added `stubbles\lang\reflect\parametersOfConstructor()`
  * added `stubbles\lang\reflect\parameter()`
  * added `stubbles\lang\reflect\constructorParameter()`
  * added `stubbles\predicate\ContainsAnyOf`
  * implemented #122: add support for ::class in value parser
  * added `stubbles\lang\Sequence::mapKeys()`
  * `stubbles\lang\iterator\MappingIterator` can now work with key mapping only


5.2.0 (2014-11-16)
------------------

  * added `stubbles\lang\Sequence`
  * added `stubbles\streams\linesOf()`


5.1.2 (2014-10-13)
------------------

  * added `stubbles\peer\Uri::addParams()`


5.1.1 (2014-10-03)
------------------

  * fixed bug: transposing a parsed uri forgot any parameters changed in query string


5.1.0 (2014-09-29)
------------------

### BC breaks

  * Setter injection is now discouraged and disabled by default, and will be removed with 6.0.0.
    * Reenable the old behaviour with `stubbles\ioc\Binder::enableSetterInjection()`


### Other changes

  * IoC now supports default param values for non-optional injections:
    If no binding present for a param but the param has a default value the
    default value will be used for injection.
  * implemented #117: injection stack on binding exceptions
  * improved performance of annotation parsing
  * improved annotation cache storage functions api
  * properties from config.ini are also now available as instance of `stubbles\lang\Properties`, named `config.ini`


5.0.1 (2014-09-01)
------------------

  * fixed issue #119: stubbles\peer\ParsedUri should catch IllegalArgumentException from stubbles\peer\QueryString


5.0.0 (2014-08-17)
------------------

### BC breaks

  * Removed `stubbles\ioc\App::createModeBindingModule($projectPath, $mode = null)`
    * project path and mode are now bound automatically if not explicitly specified
    * to overrule or configure the defaults use `stubbles\ioc\App::runtime($mode = null)` instead
  * A `__bindings()` method within an app doesn't receive the project path any more. If it is still required call `self::projectPath()`
  * Deprecated `stubbles\ioc\App::bindCurrentWorkingDirectory()`, use `stubbles\ioc\App::currentWorkingDirectory()` instead, will be removed with 6.0.0
  * Deprecated `stubbles\ioc\App::bindHostname()`, use `stubbles\ioc\App::hostname()` instead, will be removed with 6.0.0
  * Removed possibility to change values on annotations, annotations should be read only.
  * It is now possible to have more than one annotation of the same type. Retrieving only one annotation via one of the following methods will only return the first defined one:
    * `stubbles\lang\reflect\ReflectionClass::annotation()`
    * `stubbles\lang\reflect\ReflectionObject::annotation()`
    * `stubbles\lang\reflect\ReflectionFunction::annotation()`
    * `stubbles\lang\reflect\ReflectionMethod::annotation()`
    * `stubbles\lang\reflect\ReflectionParameter::annotation()`
    * `stubbles\lang\reflect\ReflectionProperty::annotation()`
  * Deprecated `stubbles\lang\reflect\Reflection*::getAnnotation()`, use `stubbles\lang\reflect\Reflection*::annotation()` instead
  * Retrieving a non-existing value from `stubbles\lang\reflect\annotation\Annotation` via method will throw a `BadMethodCallException` instead of `stubbles\lang\exception\MethodNotSupportedException`
  * Parsing `null` with any of the `stubbles\lang\Parse` methods will now always return null.
  * Removed all classes, methods and functions deprecated with 4.0.0 and 4.1.0
  * The `stubbles\lang\exception\IllegalAccessException` is now also an instance of `LogicException`. It is recommended to use the latter in catch statements, as this increases interoperability.
  * The `stubbles\lang\exception\IllegalArgumentException` is now also an instance of `InvalidArgumentException`. It is recommended to use the latter in catch statements, as this increases interoperability.
  * The `stubbles\lang\exception\IllegalStateException` is now also an instance of `LogicException`. It is recommended to use the latter in catch statements, as this increases interoperability.
  * The `stubbles\lang\exception\MethodInvocationException` is now also an instance of `BadMethodCallException`. It is recommended to use the latter in catch statements, as this increases interoperability.
  * The `stubbles\lang\exception\MethodNotSupportedException` is now also an instance of `BadMethodCallException`. It is recommended to use the latter in catch statements, as this increases interoperability.
  * Deprecated several exceptions where build-in exceptions in PHP exist, will be removed with 6.0.0:
    * `stubbles\lang\exception\IllegalAccessException`, use `LogicException` instead
    * `stubbles\lang\exception\IllegalArgumentException`, use `InvalidArgumentException` instead
    * `stubbles\lang\exception\IllegalStateException`, use `LogicException` instead
    * `stubbles\lang\exception\MethodInvocationException`, use `BadMethodCallException` instead
    * `stubbles\lang\exception\MethodNotSupportedException`, use `BadMethodCallException` instead
    * `stubbles\lang\exception\RuntimeException`, use native `RuntimeException` instead
  * Deprecated `stubbles\lang\exception\Throwable`, will be removed with 6.0.0


### Other changes

  * Added possibility to retrieve all annotations for an element:
    * `stubbles\lang\reflect\ReflectionClass::annotations()`
    * `stubbles\lang\reflect\ReflectionObject::annotations()`
    * `stubbles\lang\reflect\ReflectionFunction::annotations()`
    * `stubbles\lang\reflect\ReflectionMethod::annotations()`
    * `stubbles\lang\reflect\ReflectionParameter::annotations()`
    * `stubbles\lang\reflect\ReflectionProperty::annotations()`
  * Added non-static usage of `stubbles\lang\Parse`
    * instance creation takes a string value
    * all methods `to*()` are additionally available as non-static `as*()` methods except `toType()`
  * Added `stubbles\lang\Properties::parse()` which returns an instance of `stubbles\lang\Parse`
  * Added `stubbles\lang\reflect\annotation\Annotation::parse()` which returns an instance of `stubbles\lang\Parse`
  * Added `stubbles\lang\iterator\RecursiveArrayIterator` to iterate recursively on leafs of arrays only
  * Added `stubbles\lang\iterator\MappingIterator` to allow mapping of keys and values during iteration


4.1.4 (2014-08-11)
------------------

  * added support to reflect array callbacks with `stubbles\lang\reflect()`


4.1.3 (2014-08-10)
------------------

  * fixed bug that property bindings did not work together with type hints, e.g. for `stubbles\lang\SecureString`


4.1.2 (2014-08-10)
------------------

  * fixed bug in `stubbles\lang\exception\Exception::__toString()` always reporting this class instead of the real exception class
  * all properties in `stubbles\lang\Properties` where key ends with `password` are automatically stored as `stubbles\lang\SecureString`
  * fixed bug that a `var_dump()` of a `stubbles\lang\SecureString` instance reveiled the length of the secured string


4.1.1 (2014-08-09)
------------------

  * fixed bug with scheme transposing and default ports for http uris


4.1.0 (2014-08-08)
------------------

### BC breaks

  * `stubbles\lang\Properties::parseBool()` on property value `'1'` does not yield `true` any more, use one of `'true'`, `'yes'` or `'on'` instead


### Other changes

  * added `stubbles\lang\Parse`
    * unified string to value parsing for properties and annotation values
    * properties bound via `stubbles\ioc\Binder::bindProperties()` are now injected as parsed values instead of as string values only
` * added `stubbles\lang\Properties::parseValue()`, deprecated other parsing methods, will be removed with 5.0.0
    * `parseString()
    * `parseInt()`
    * `parseFloat()`
    * `parseBool()`
    * `parseArray()`
    * `parseHash()`
    * `parseRange()`


4.0.2 (2014-08-06)
------------------

  * fixed bug: transposing `stubbles\peer\http\HttpUri` to another scheme must change the port


4.0.1 (2014-08-05)
------------------

  * ensure `stubbles\predicate\IsExistingDirectory` and `stubbles\predicate\IsExistingFile` use current working directoy when no base path given


4.0.0 (2014-07-31)
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
  * major API rework: replaced some constructs with better ones, all deprecated will be removed with 5.0.0
    * deprecated `stubbles\lang\Properties::getSections()`, iterate over instance instead
    * deprecated `stubbles\lang\Properties::hasSection()`, use `stubbles\lang\Properties::containSection()` instead
    * deprecated `stubbles\lang\Properties::getSection()`, use `stubbles\lang\Properties::section()` instead
    * deprecated `stubbles\lang\Properties::getSectionKeys()`, use `stubbles\lang\Properties::keysForSection()` instead
    * deprecated `stubbles\lang\Properties::hasValue()`, use `stubbles\lang\Properties::containValue()` instead
    * deprecated `stubbles\lang\Properties::getValue()`, use `stubbles\lang\Properties::value()` instead
    * deprecated `stubbles\lang\ResourceLoader::getProjectResourceUri()´, use `stubbles\lang\ResourceLoader::open()` or `stubbles\lang\ResourceLoader::load()` instead
    * deprecated `stubbles\lang\ResourceLoader::getResourceUris()´, use `stubbles\lang\ResourceLoader::availableResourceUris()` instead
    * deprecated `stubbles\lang\ResourceLoader::getRootPath()´ and `stubbles\lang\ResourceLoader::getRoot()´, use `stubbles\lang\Rootpath` instead
    * deprecated `stubbles\peer\http\AcceptHeader::getList()`
    * deprecated `stubbles\peer\BsdSocket::getType()`, use `stubbles\peer\BsdSocket::type()` instead
    * deprecated `stubbles\peer\BsdSocket::getOption()`, use `stubbles\peer\BsdSocket::option()` instead
    * deprecated `stubbles\peer\Socket::getTimeout()`, use `stubbles\peer\Socket::timeout()` instead
    * deprecated `stubbles\peer\Socket::getPrefix()`, use `stubbles\peer\Socket::usesSsl()` instead
    * deprecated `stubbles\peer\Socket::getInputStream()`, use `stubbles\peer\Socket::in()` instead
    * deprecated `stubbles\peer\Socket::getOutputStream()`, use `stubbles\peer\Socket::out()` instead
    * deprecated `stubbles\peer\Uri::getScheme()`, use `stubbles\peer\Uri::scheme()` instead
    * deprecated `stubbles\peer\Uri::getUser()`, use `stubbles\peer\Uri::user()` instead
    * deprecated `stubbles\peer\Uri::getPassword()`, use `stubbles\peer\Uri::password()` instead
    * deprecated `stubbles\peer\Uri::getHost()`, use `stubbles\peer\Uri::hostname()` instead
    * deprecated `stubbles\peer\Uri::getPort()`, use `stubbles\peer\Uri::port()` instead
    * deprecated `stubbles\peer\Uri::getPath()`, use `stubbles\peer\Uri::path()` instead
    * deprecated `stubbles\peer\Uri::getQueryString()`, use `stubbles\peer\Uri::queryString()` instead
    * deprecated `stubbles\peer\Uri::getParam()`, use `stubbles\peer\Uri::param()` instead
    * deprecated `stubbles\peer\Uri::getFragment()`, use `stubbles\peer\Uri::fragment()` instead
    * deprecated `stubbles\peer\http\Http::getStatusClass()`, use `stubbles\peer\http\Http::statusClassFor()` instead
    * deprecated `stubbles\peer\http\Http::getStatusCodes()`, use `stubbles\peer\http\Http::statusCodes()` instead
    * deprecated `stubbles\peer\http\Http::getReasonPhrase()`, use `stubbles\peer\http\Http::reasonPhraseFor()` instead
    * deprecated `stubbles\peer\http\Http::VERSION_1_0`, use `stubbles\peer\http\HttpVersion::HTTP_1_0` instead
    * deprecated `stubbles\peer\http\Http::VERSION_1_1`, use `stubbles\peer\http\HttpVersion::HTTP_1_1` instead
    * deprecated `stubbles\peer\http\Http::isVersionValid()`, use `stubbles\peer\http\HttpVersion` instead
    * deprecated `stubbles\peer\http\HttpResponse::getStatusLine()`, use `stubbles\peer\http\HttpResponse::statusLine()` instead
    * deprecated `stubbles\peer\http\HttpResponse::getHttpVersion()`, use `stubbles\peer\http\HttpResponse::httpVersion()` instead
    * deprecated `stubbles\peer\http\HttpResponse::getStatusCode()`, use `stubbles\peer\http\HttpResponse::statusCode()` instead
    * deprecated `stubbles\peer\http\HttpResponse::getStatusCodeClass()`, use `stubbles\peer\http\HttpResponse::statusCodeClass()` instead
    * deprecated `stubbles\peer\http\HttpResponse::getReasonPhrase()`, use `stubbles\peer\http\HttpResponse::reasonPhrase()` instead
    * deprecated `stubbles\peer\http\HttpResponse::getHeader()`, use `stubbles\peer\http\HttpResponse::headers()` instead
    * deprecated `stubbles\peer\http\HttpResponse::getBody()`, use `stubbles\peer\http\HttpResponse::body()` instead
    * deprecated `stubbles\peer\streams\memory\MemoryOutputStream::getBuffer()`, use `stubbles\peer\streams\memory\MemoryOutputStream::buffer()` instead
    * deprecated `stubbles\peer\streams\filter\StreamFilter`, use predicates instead
    * deprecated `stubbles\peer\streams\filter\CompositeStreamFilter`, use predicates instead
  * deprecated `stubbles\peer\BsdSocket::getDomain()`, will be removed with 5.0.0
  * deprecated `stubbles\peer\Socket::getHost()`, will be removed with 5.0.0
  * deprecated `stubbles\peer\Socket::getPort()`, will be removed with 5.0.0
  * `stubbles\peer\Socket::getInputStream()` and `stubbles\peer\Socket::getOutputStream()` will now always return the same instance
  * `stubbles\peer\http\HttpUri::fromString()` no longer accepts uris with userinfo by default in compliance with RFC 7230, to retain the old behaviour pass `stubbles\peer\http\Http::RFC_2616` as second parameter


### Other changes

  * `stubbles\lang\ResourceLoader` now supports PSR-4
  * fixed bug: `stubbles\lang\ModifiableProperties::merge()` now returns `stubbles\lang\ModifiableProperties::merge()` instead of `stubbles\lang\Properties::merge()` only
  * added `stubbles\lang\ModifiableProperties::unmodifiable()`
  * added `stubbles\lang\Rootpath`
  * added `stubbles\lang\SecureString`
  * added `stubbles\lang\reflect\annotation\Annotation::targetName()` which returns the name of the class, method, function, property or parameter of where the annotation comes from
  * added `stubbles\peer\http\Http::RFC_2616` and `stubbles\peer\http\Http::RFC_7230`
  * added `stubbles\peer\http\Http::isValidRfc()`
  * added `stubbles\peer\http\Http::lines()`
  * added `stubbles\peer\http\Http::OPTIONS`
  * added `stubbles\peer\http\HttpVersion`
  * added `stubbles\peer\http\HttpUri::castFrom()`
  * added `stubbles\peer\http\HttpUri::fromParts()`
  * allowed conversion of `stubbles\peer\streams\memory\MemoryOutputStream` to a string, will contain buffer content
  * added `stubbles\peer\http\emptyAcceptHeader()`
  * `net\stubbles\ioc\App::createModeBindingModule()` now accepts a callable as second parameter which returns a mode
  * added `stubbles\predicate`:
    * `stubbles\predicate\Predicate` as abstract base implementation
    * `stubbles\predicate\CallablePredicate` to wrap something callable as a predicate
    * `stubbles\predicate\Contains`
    * `stubbles\predicate\Equals`
    * `stubbles\predicate\IsExistingDirectory`
    * `stubbles\predicate\IsExistingFile`
    * `stubbles\predicate\IsExistingHttpUri`
    * `stubbles\predicate\IsHttpUri`
    * `stubbles\predicate\IsIpAddress`
    * `stubbles\predicate\IsIpV4Address`
    * `stubbles\predicate\IsIpV6Address`
    * `stubbles\predicate\IsMailAddress`
    * `stubbles\predicate\IsOneOf`
    * `stubbles\predicate\Regex`
  * `stubbles\lang\reflect()` can now also reflect functions
  * `stubbles\lang\reflect()` now throws a `stubbles\lang\exception\IllegalArgumentException` if the value can not be reflected
  * added `stubbles\lang\ensureCallable()`
  * added `stubbles\peer\IpAddress`


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
