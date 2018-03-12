<?php

namespace Vortexgin\TechEditorialBundle\Manager;

use Vortexgin\CoreBundle\Manager\RedisManager;
use Vortexgin\CoreBundle\Util\Validator;
use Vortexgin\MorphAnalyzerBundle\Manager\MorphManager;

/**
 * Final class of tech editorial manager
 * 
 * @category Manager
 * @package  Vortexgin\TechEditorialBundle\Manager
 * @author   Tommy <vortexgin@gmail.com>
 * @license  GPL v3
 * @link     https://github.com/vortexgin
 */
class TechEditorialManager
{

    private $_dictionaryManager;
    
    private $_redisManager;

    private $_dictionary = [];

    /**
     * Construct of manager
     * 
     * @param \Vortexgin\TechEditorialBundle\Manager\DictionaryManager $dictionaryManager Manager of dictionary
     * @param \Vortexgin\CoreBundle\Manager\RedisManager               $redisManager      Manager of redis
     */
    public function __construct(DictionaryManager $dictionaryManager, RedisManager $redisManager)
    {
        $this->_dictionaryManager = $dictionaryManager;
        $this->_redisManager = $redisManager;

        $this->_redisManager->switchDB('snc_redis.result');
        $this->dictionary = $this->_redisManager->getData('tech_dictionary', array());
        if (!$this->dictionary) {
            $this->updateDictionary();
        }
    }

    /**
     * Function to analyze word
     * 
     * @param string $word Word to analyze
     * 
     * @return string
     */
    public function analyze($word)
    {
        try {
            $morphManager = new MorphManager();
            $morphological = $morphManager->analyze($word);
            if (!$morphological) {
                return $word;
            }
            
            foreach ($morphological['morph'] as $value) {
                if ($value['lemma'] == 'Unknown') {
                    $word = str_replace($value['word'], ucfirst($value['word']), $word);
                }
            }

            foreach ($this->dictionary as $dictionary) {
                if (Validator::validate($dictionary, 'regex', null, 'empty')) {
                    $word = preg_replace(sprintf("/(%s)\ /im", $dictionary['regex']), sprintf("%s ", $dictionary['replaceWith']), $word);
                } else {
                    $word = preg_replace(sprintf("/(%s)\ /im", $dictionary['word']), sprintf("%s ", $dictionary['replaceWith']), $word);
                    //$word = preg_replace("/\ ({$dictionary['word']})\ /im", $dictionary['replaceWith'], $word);
                }
                
                if (Validator::validate($dictionary, 'typo', 'array', 'empty')) {
                    foreach ($dictionary['typo'] as $typo) {
                        $word = preg_replace(sprintf("/(%s)\ /im", $typo), sprintf("%s ", $dictionary['replaceWith']), $word);
                    }
                }
            }
            
            return $word;    
        } catch (\Exception $e) {
            return $word;
        }        
    }

    /**
     * Function to update dictionary cache
     * 
     * @return null
     */
    public function updateDictionary()
    {
        $count = $this->_dictionaryManager->count(array());
        $get = $this->_dictionaryManager->get(array(), 'id', 'ASC', 1, $count[1]);
        if (count($get) > 0) {
            $this->dictionary = [];
            foreach ($get as $value) {
                $this->dictionary[] = $this->_dictionaryManager->serialize($value);
            }
            $this->_redisManager->setCache('tech_dictionary', $this->dictionary);
        }
    }
}
