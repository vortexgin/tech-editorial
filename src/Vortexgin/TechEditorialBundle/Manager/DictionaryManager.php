<?php

namespace Vortexgin\TechEditorialBundle\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Cache\ArrayCache;
use Vortexgin\CoreBundle\Manager\Manager;
use Vortexgin\CoreBundle\Util\Validator;
use Vortexgin\TechEditorialBundle\Entity\Dictionary;

/**
 * Final class of dictionary manager
 * 
 * @category Manager
 * @package  Vortexgin\TechEditorialBundle\Manager
 * @author   Tommy <vortexgin@gmail.com>
 * @license  GPL v3
 * @link     https://github.com/vortexgin
 */
final class DictionaryManager extends Manager
{

    /**
     * Construct of manager
     * 
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container Symfony Container
     * @param string                                                    $class     Class Name
     */
    public function __construct(ContainerInterface $container, $class)
    {
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');
        $this->container = $container;

        $this->listSearchFields = ['id', 'word', 'type', 'regex', 'replaceWith', 'typo'];
        $this->listOrderBy = $this->listSearchFields;

        $this->manager = $container->get('doctrine.orm.editorial_entity_manager');
        $this->repository = $this->manager->getRepository($class);
        $this->class = $this->manager->getClassMetadata($class)->getName();
        $this->classObject = $class;
        $this->request = $container->get('request');

        $this->timeInit = new \DateTime();

        $cache = $this->manager->getConfiguration()->getHydrationCacheImpl();
        $this->cache = $cache ?: new ArrayCache();
        //parent::__construct($container->get('request'), $container->get('editorial'), $class);
    }

    /**
     * Function to check if object is supported
     * 
     * @param \Vortexgin\TechEditorialBundle\Entity\Dictionary $object Object of entity
     *
     * @return boolean
     */
    protected function isSupportedObject($object)
    {
        if ($object instanceof Dictionary)
            return true;

        return false;
    }

    /**
     * Function to serialize object entity into array
     * 
     * @param \Vortexgin\TechEditorialBundle\Entity\Dictionary $object Object of entity
     *
     * @return array
     */
    public function serialize($object)
    {
        try {
            if (! $this->isSupportedObject($object))
                return false;

            $this->return = array(
                'id' => $object->getId(),
                'word' => $object->getWord(),
                'type' => $object->getType(),
                'regex' => $object->getRegex(),
                'replaceWith' => $object->getReplaceWith(),
                'typo' => $object->getTypo(),
                'created_at' => $object->getCreatedAt()?$object->getCreatedAt()->format('d-m-Y H:i:s'):null,
                'updated_at' => $object->getUpdatedAt()?$object->getUpdatedAt()->format('d-m-Y H:i:s'):null,
            );

            return $this->return;
        } catch(\Exception $e) {
            return $this->return;
        }
    }

    /**
     * Funtion to query entity
     * 
     * @param array  $param     Array of param
     * @param string $orderBy   Order fields
     * @param string $orderSort Order direction
     * @param int    $page      Number of page
     * @param int    $count     Count of element
     * 
     * @return mixed
     */
    public function get(array $param = array(), $orderBy = 'id', $orderSort = 'DESC', $page = 1, $count = 20)
    {
        list($orderBy, $orderSort, $offset, $limit) = $this->generateDefaultParam($orderBy, $orderSort, $page, $count);

        $sql = $this->generateQuery($param);
        $sql->select('er')
            ->orderBy($orderBy, $orderSort)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $this->getResult($sql);
    }

    /**
     * Funtion to count entity
     * 
     * @param array $param Array of param
     * 
     * @return mixed
     */
    public function count(array $param = array())
    {
        $sql = $this->generateQuery($param);
        $sql->select('count(er.id)');

        return $this->getOneOrNullResult($sql);
    }

    /**
     * Funtion to insert entity
     * 
     * @param array $param Array of param
     * 
     * @return mixed
     */
    public function insert(array $param = array())
    {
        try {
            $obj = $this->createNew();
            $obj->setWord($param['word'])
                ->setType($param['type'])
                ->setReplaceWith($param['replace_with'])
                ->setCreatedBy($param['user_log']);

            return $this->update($obj, $param);
        } catch (\Exception $e) {
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return false;
        }
    }

    /**
     * Funtion to update entity
     * 
     * @param \Vortexgin\TechEditorialBundle\Entity\Dictionary $obj   Object of entity
     * @param array                                            $param Array of param
     * 
     * @return mixed
     */
    public function update(Dictionary $obj, $param)
    {
        try {
            if (Validator::validate($param, 'word', null, 'empty')) {
                $obj->setWord($param['word']);
            }
            if (Validator::validate($param, 'type', null, 'empty')) {
                $obj->setType($param['type']);
            }
            if (Validator::validate($param, 'regex', null, 'empty')) {
                $obj->setRegex($param['regex']);
            }
            if (Validator::validate($param, 'replace_with', null, 'empty')) {
                $obj->setReplaceWith($param['replace_with']);
            }
            if (Validator::validate($param, 'typo', 'array', 'empty')) {
                $obj->setTypo($param['typo']);
            }

            $obj->setUpdatedBy($param['user_log']);
            $this->manager->persist($obj);
            if (!empty($obj->getId())) {
                $this->logModified($obj, $param['user_log']);
            }
            $this->manager->flush();

            return $obj;
        } catch (\Exception $e) {
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return false;
        }
    }
}