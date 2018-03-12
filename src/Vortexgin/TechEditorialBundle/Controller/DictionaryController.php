<?php

namespace Vortexgin\TechEditorialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Vortexgin\CoreBundle\Controller\BaseController;
use Vortexgin\CoreBundle\Util\HttpStatusHelper;
use Vortexgin\CoreBundle\Util\Validator;
use Vortexgin\TechEditorialBundle\Utils\DictionaryValidator;
use Vortexgin\TechEditorialBundle\Utils\DictionaryFilterGenerator;

/**
 * Controller of Dictionary
 * 
 * @category Manager
 * @package  Vortexgin\TechEditorialBundle\Manager
 * @author   Tommy <vortexgin@gmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/vortexgin
 */
class DictionaryController extends BaseController
{

    /**
     * API Endpoint to create dictionary
     * 
     * @param Symfony\Component\HttpFoundation\Request $request Request Http
     * 
     * @ApiDoc(
     *      section="Tools",
     *      resource="Tech Editorial",
     *      description="Add an dictionary",
     *      parameters={
     *          {"name"="word",         "dataType"="string", "required"=true,  "description"="word"},
     *          {"name"="type",         "dataType"="string", "required"=true,  "description"="type of word"},
     *          {"name"="replace_with", "dataType"="string", "required"=true,  "description"="replacing word"},
     *          {"name"="regex",        "dataType"="string", "required"=false, "description"="regex word"},
     *          {"name"="typo[0]",      "dataType"="string", "required"=false, "description"="list of typo word"},
     *      },
     *      statusCodes={
     *          201="Returned when successful",
     *          400="Bad request",
     *          500="System error",
     *      }
     * )
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        try {
            $post = $request->request->all();
            
            /** @var $dictionaryManager \Vortexgin\TechEditorialBundle\Manager\DictionaryManager */
            $dictionaryManager = $this->container->get('vortexgin.techeditorial.manager.dictionary');

            // request validation
            $validate = DictionaryValidator::validate($post);
            if ($validate !== true) {
                return $this->errorResponse($validate, HttpStatusHelper::HTTP_BAD_REQUEST);
            }

            $param = array(
                'word' => $post['word'],
                'type' => $post['type'],
                'replace_with' => $post['replace_with'],
                'regex' => Validator::validate($post, 'regex', null, 'empty')?$post['regex']:null,
                'typo' => Validator::validate($post, 'typo', 'array', 'empty')?$post['typo']:null,
                'user_log' => $this->user?$this->user->getUsername(): 'ANONYMOUS',
            );
            
            $dictionary = $dictionaryManager->insert($param);
            if (!$dictionary) {
                return $this->errorResponse('Create dictionary failed, Please try again later', HttpStatusHelper::HTTP_EXPECTATION_FAILED);
            }
                        
            return $this->successResponse(
                array(
                    'dictionary' => $dictionaryManager->serialize($dictionary), 
                ), 
                HttpStatusHelper::HTTP_CREATED
            );
        } catch (\Exception $e) {
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return $this->errorResponse('Create dictionary failed, Please try again later. '.$e->getMessage(), HttpStatusHelper::HTTP_PRECONDITION_FAILED);
        }
    }
    
    /**
     * API Endpoint to read dictionary
     * 
     * @param Symfony\Component\HttpFoundation\Request $request Request Http
     * 
     * @ApiDoc(
     *      section="Tools",
     *      resource="Tech Editorial",
     *      description="Read dictionary",
     *      parameters={
     *          {"name"="limit",        "dataType"="integer", "required"=false, "description"="data limit, default 20"},
     *          {"name"="page",         "dataType"="integer", "required"=false, "description"="data offset, default 0"},
     *          {"name"="order_by",     "dataType"="string", "required"=false, "format"="id|expired_date", "description"="data order by, default id"},
     *          {"name"="order_type",   "dataType"="string", "required"=false, "format"="ASC|DESC", "description"="data order type, default DESC"},
     *          {"name"="id",           "dataType"="string", "required"=false, "description"="id of dictionary"},
     *      },
     *      statusCodes={
     *          200="Returned when successful",
     *          400="Bad request",
     *          500="System error",
     *      }
     * )
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */    
    public function readAction(Request $request)
    {
        try {
            $get = $request->query->all();
            
            /** @var $dictionaryManager \Vortexgin\TechEditorialBundle\Manager\DictionaryManager */
            $dictionaryManager = $this->container->get('vortexgin.techeditorial.manager.dictionary');
            
            $filter = DictionaryFilterGenerator::generateFilter($get);
            list($orderBy, $orderSort, $limit, $page) = $this->extractDefaultParameter($dictionaryManager, $get);

            $listDictionary = $dictionaryManager->get($filter, $orderBy, $orderSort, $page, $limit);
            $totalDictionary = $dictionaryManager->count($filter);

            if (count($listDictionary) <= 0) {
                return $this->errorResponse('Dictionary not found', HttpStatusHelper::HTTP_NOT_FOUND);
            }
            
            $data = array();
            foreach ($listDictionary as $key => $value) {
                $data[] = $dictionaryManager->serialize($value);
            }
            
            return $this->successResponse(
                array(
                    'dictionary' => $data,
                    'count' => array(
                        'total' => count($listDictionary),
                        'all' => (int) $totalDictionary,
                    ),
                )
            );
        }  catch (\Exception $e) {
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return $this->errorResponse('Read dictionary failed, Please try again later. '.$e->getMessage(), HttpStatusHelper::HTTP_PRECONDITION_FAILED);
        }
        
    }
    
    /**
     * API Endpoint to update dictionary
     * 
     * @param Symfony\Component\HttpFoundation\Request $request Request Http
     * @param integer                                  $id      ID of dictionary to update
     * 
     * @ApiDoc(
     *      section="Tools",
     *      resource="Tech Editorial",
     *      description="Update dictionary",
     *      parameters={
     *          {"name"="word",         "dataType"="string", "required"=false, "description"="word"},
     *          {"name"="type",         "dataType"="string", "required"=false, "description"="type of word"},
     *          {"name"="replace_with", "dataType"="string", "required"=false, "description"="replacing word"},
     *          {"name"="regex",        "dataType"="string", "required"=false, "description"="regex word"},
     *          {"name"="typo[0]",      "dataType"="string", "required"=false, "description"="list of typo word"},
     *      },
     *      statusCodes={
     *          202="Returned when successful",
     *          400="Bad request",
     *          500="System error",
     *      }
     * )
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, $id)
    {
        try{
            $post = $request->request->all();

            /** @var $dictionaryManager \Vortexgin\TechEditorialBundle\Manager\DictionaryManager */
            $dictionaryManager = $this->container->get('vortexgin.techeditorial.manager.dictionary');

            $detail = $dictionaryManager->get(array(array('id', $id)));
            if (!$detail) {
                return $this->errorResponse('Dictionary not found', HttpStatusHelper::HTTP_NOT_FOUND);
            }
            $dictionary = $detail[0];

            $param = array(
                'word' => Validator::validate($post, 'word', null, 'empty')?$post['word']:null,
                'type' => Validator::validate($post, 'type', null, 'empty')?$post['type']:null,
                'replace_with' => Validator::validate($post, 'replace_with', null, 'empty')?$post['replace_with']:null,
                'regex' => Validator::validate($post, 'regex', null, 'empty')?$post['regex']:null,
                'typo' => Validator::validate($post, 'typo', 'array', 'empty')?$post['typo']:null,
                'user_log' => $this->user?$this->user->getUsername(): 'ANONYMOUS',
            );
            $newDictionary = $dictionaryManager->update($dictionary, $param);
            if (!$newDictionary) {
                return $this->errorResponse('Update dictionary failed, Please try again later', HttpStatusHelper::HTTP_EXPECTATION_FAILED);
            }
                        
            return $this->successResponse(
                array(
                    'dictionary'=> $dictionaryManager->serialize($newDictionary),
                ), 
                HttpStatusHelper::HTTP_ACCEPTED
            );

            return $this->successResponse(array(), HttpStatusHelper::HTTP_ACCEPTED);
        }catch(\Exception $e){
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return $this->errorResponse('Update dictionary failed, Please try again later. '.$e->getMessage(), HttpStatusHelper::HTTP_PRECONDITION_FAILED);
        }
    }
    
    /**
     * API Endpoint to delete dictionary
     * 
     * @param Symfony\Component\HttpFoundation\Request $request Request Http
     * @param integer                                  $id      ID of dictionary to delete
     * 
     * @ApiDoc(
     *      section="Tools",
     *      resource="Tech Editorial",
     *      description="Delete dictionary",
     *      statusCodes={
     *          204="Returned when successful",
     *          400="Bad request",
     *          500="System error",
     *      }
     * )
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        try{
            /** @var $dictionaryManager \Vortexgin\TechEditorialBundle\Manager\DictionaryManager */
            $dictionaryManager = $this->container->get('vortexgin.techeditorial.manager.dictionary');

            $detail = $stickyManager->get(array(array('id', $id)));
            if (!$detail) {
                return $this->errorResponse('Dictionary not found', HttpStatusHelper::HTTP_NOT_FOUND);
            }

            $dictionaryManager->setUser($this->user->getUsername()?:'ANONYMOUS');
            $dictionaryManager->delete($detail[0]);

            return $this->successResponse(array(), HttpStatusHelper::HTTP_NO_CONTENT);
        }catch(\Exception $e){
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return $this->errorResponse('Delete dictionary failed, Please try again later. '.$e->getMessage(), HttpStatusHelper::HTTP_PRECONDITION_FAILED);
        }
    }
}
