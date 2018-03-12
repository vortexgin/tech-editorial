<?php

namespace Vortexgin\TechEditorialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Vortexgin\CoreBundle\Controller\BaseController;
use Vortexgin\CoreBundle\Util\HttpStatusHelper;
use Vortexgin\CoreBundle\Util\Validator;

/**
 * Controller of Tech Editorial
 * 
 * @category Manager
 * @package  Vortexgin\TechEditorialBundle\Manager
 * @author   Tommy <vortexgin@gmail.com>
 * @license  Apache 2.0
 * @link     https://github.com/vortexgin
 */
class TechEditorialController extends BaseController
{

    /**
     * API Endpoint to analyze as editor of technology
     * 
     * @param Symfony\Component\HttpFoundation\Request $request Request Http
     * 
     * @ApiDoc(
     *      section="Tools",
     *      resource="Tech Editorial",
     *      description="Analyze a word",
     *      parameters={
     *          {"name"="word", "dataType"="string", "required"=true,  "description"="word"},
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
    public function analyzeAction(Request $request)
    {
        try {
            $post = $request->request->all();
            
            /** @var $editorialManager \Vortexgin\TechEditorialBundle\Manager\TechEditorialManager */
            $editorialManager = $this->container->get('vortexgin.techeditorial.manager');

            // request validation
            if (!Validator::validate($post, 'word', null, 'empty')) {
                return $this->errorResponse('Please insert word', HttpStatusHelper::HTTP_BAD_REQUEST);
            }

            return $this->successResponse(
                array(
                    'word' => $editorialManager->analyze($post['word']), 
                ), 
                HttpStatusHelper::HTTP_CREATED
            );
        } catch (\Exception $e) {
            $this->container->get('logger')->error(sprintf($e->getMessage()));
            return $this->errorResponse('Analyze word failed, Please try again later. '.$e->getMessage(), HttpStatusHelper::HTTP_PRECONDITION_FAILED);
        }
    }
    
}