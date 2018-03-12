<?php

namespace Vortexgin\TechEditorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vortexgin\CoreBundle\Entity\Base as BaseEntity;

/**
 * Dictionary
 * 
 * @category Entity
 * @package  Vortexgin\TechEditorialBundle\Entity
 * @author   Tommy <vortexgin@gmail.com>
 * @license  GPL v3
 * @link     https://github.com/vortexgin
 *
 * @ORM\Table(name="editorial_dictionary")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Dictionary extends BaseEntity
{
    /**
     * Word of dictionary
     * 
     * @var string
     *
     * @ORM\Column(name="word", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $word;

    /**
     * Type of word
     * 
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $type;

    /**
     * Regex of word
     * 
     * @var string
     *
     * @ORM\Column(name="regex", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    protected $regex;

    /**
     * Replace word with
     * 
     * @var string
     *
     * @ORM\Column(name="replaceWith", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $replaceWith;

    /**
     * List of typo
     * 
     * @var string
     *
     * @ORM\Column(name="typo", type="text",precision=0, scale=0, nullable=true, unique=false)
     */
    protected $typo;

    /**
     * Function to get word of dictionary
     * 
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }
    
    /**
     * Function to set word of dictionary
     * 
     * @param string $word Word of dictionary
     * 
     * @return self
     */
    public function setWord($word)
    {
        $this->word = $word;
        return $this;
    }

    /**
     * Function to get type of word
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * Function to set type of word
     * 
     * @param string $type Type of word
     * 
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Function to get regex of word
     * 
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }
    
    /**
     * Function to set regex of word
     * 
     * @param string $regex Regex of word
     * 
     * @return self
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * Function to get replace word with
     * 
     * @return string
     */
    public function getReplaceWith()
    {
        return $this->replaceWith;
    }
    
    /**
     * Function to set replace word with
     * 
     * @param string $replaceWith Replace word with
     * 
     * @return self
     */
    public function setReplaceWith($replaceWith)
    {
        $this->replaceWith = $replaceWith;
        return $this;
    }

    /**
     * Function to get list of typo
     * 
     * @return string
     */
    public function getTypo()
    {
        return json_decode($this->typo, true);
    }
    
    /**
     * Function to set list of typo
     * 
     * @param string $typo List of typo
     * 
     * @return self
     */
    public function setTypo(array $typo)
    {
        $this->typo = json_encode($typo);
        return $this;
    }

}