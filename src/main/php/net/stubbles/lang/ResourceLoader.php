<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
use net\stubbles\lang\BaseObject;
/**
 * Class to load resources from arbitrary locations.
 *
 * @since       1.6.0
 * @Singleton
 */
class ResourceLoader extends BaseObject
{
    /**
     * return all uris for a resource
     *
     * @param   string  $resourceName  the resource to retrieve the uris for
     * @return  string[]
     */
    public function getResourceUris($resourceName)
    {
        $uris = array();
        foreach (\stubBootstrap::getSourcePathes() as $resourcePath) {
            if (file_exists($resourcePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $resourceName) === true) {
                $uris[] = realpath($resourcePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $resourceName);
            }
        }

        return $uris;
    }
}
?>