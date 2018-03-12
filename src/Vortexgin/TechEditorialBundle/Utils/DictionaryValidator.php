<?php

namespace Vortexgin\TechEditorialBundle\Utils;

use Vortexgin\CoreBundle\Util\Validator;
use Vortexgin\TechEditorialBundle\Model\WordType;

/**
 * Class to validate data to entity
 * 
 * @category Utils
 * @package  Vortexgin\TechEditorialBundle\Utils
 * @author   Tommy <vortexgin@gmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/vortexgin
 */
final class DictionaryValidator
{

    /**
     * Static function to validate date to dictionary entity
     * 
     * @param array $params Parameters to filter
     * 
     * @return boolean
     */
    public static function validate(array $params = array())
    {
        try {
            if (!Validator::validate($params, 'word', null, 'empty')) {
                return 'Please insert word';
            }
            if (!Validator::validate($params, 'type', null, 'empty')) {
                return 'Please insert type';
            }
            if (!in_array($params['type'], WordType::$listType)) {
                return 'Invalid type';
            }
            if (!Validator::validate($params, 'replace_with', null, 'empty')) {
                return 'Please insert replace with';
            }

            return true;
        } catch(\Exception $e) {
            return false;
        }
    }
}