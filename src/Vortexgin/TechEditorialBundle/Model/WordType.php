<?php

namespace Vortexgin\TechEditorialBundle\Model;

/**
 * Final Class Type of Word
 * 
 * @category Model
 * @package  Vortexgin\TechEditorialBundle\Model
 * @author   Tommy <vortexgin@gmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/vortexgin
 */
final class WordType
{

    const TERM = 'term';

    const BRAND = 'brand';

    public static $listType = [
        'TERM' => self::TERM,
        'BRAND' => self::BRAND,
    ];
}