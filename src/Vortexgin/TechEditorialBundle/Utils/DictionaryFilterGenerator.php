<?php

namespace Vortexgin\TechEditorialBundle\Utils;

use Vortexgin\CoreBundle\Util\Validator;

/**
 * Class to generate filter data on entity
 * 
 * @category Utils
 * @package  Vortexgin\TechEditorialBundle\Utils
 * @author   Tommy <tommy@dailysocial.id>
 * @license  Apache 2.0
 * @link     https://minerva.dailysocial.id
 */
class DictionaryFilterGenerator
{

    /**
     * Static function to generate filter on dictionary entity
     * 
     * @param array $param Parameters to filter
     * 
     * @return array
     */
    static public function generateFilter(array $param = array()) 
    {
        try{
            $filter = array();

            if (Validator::validate($param, 'query', null, 'empty')) {
                if (Validator::validate($param, 'fields', null, 'empty')) {
                    $fields = json_decode($param['fields'], true);
                    foreach ($fields as $field) {
                        $param[$field] = $param['query'];
                    }
                } else {
                    $param['name'] = $param['query'];
                }
            }

            if (Validator::validate($param, 'id', null, 'empty')) {
                $filter[] = array('id', $param['id']);
            }
            if (Validator::validate($param, 'word', null, 'empty')) {
                $filter[] = array('word', $param['word'], 'like');
            }
            if (Validator::validate($param, 'type', null, 'empty')) {
                $filter[] = array('type', $param['type']);
            }
            if (Validator::validate($param, 'regex', null, 'empty')) {
                $filter[] = array('regex', $param['regex'], 'like');
            }
            if (Validator::validate($param, 'replace_with', null, 'empty')) {
                $filter[] = array('replaceWith', $param['replace_with'], 'like');
            }
            if (Validator::validate($param, 'typo', null, 'empty')) {
                $filter[] = array('typo', $param['typo'], 'like');
            }

            if (Validator::validate($param, 'term', null, 'empty')) {
                $filter[] = array('word', $param['term'], 'like');
            }

            return $filter;
        }catch(\Exception $e){
            return array();
        }
    }
}
