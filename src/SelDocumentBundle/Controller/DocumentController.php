<?php

namespace SelDocumentBundle\Controller;

use SelServiceBundle\Entity\ServiceManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SelDocumentBundle\Entity\Document;

class DocumentController extends Controller
{
    /**
     * @Route("/ajaxupload", name="ajax_file_upload", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     */
    public function fileUploadAction(Request $request)
    {
        $document_manager = $this->getDocumentManager();
        $key = $request->files->keys()[0];
        $media = $request->files->get($key)['documents']['__name__']['file'][0];
        $subfolder = $request->query->get('subfolder');

        $document = $document_manager->createDocument($media, $subfolder);

        $document_manager->saveDocument($document);

        return new JsonResponse(array(
            'url' => $document->getWebPath(),
            'path' => $document->getPath(),
        ));
    }

    /**
     * @Route("/ajaxdelete", name="ajax_file_delete", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     */
    public function fileDeleteAction(Request $request) {
        $path = $request->query->get('path');
        $index = $request->query->get('index');
        $subFolder = $request->query->get('subfolder');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("SelDocumentBundle:Document");

        $doc = $repo->findOneBy(array("path" => $path, "subfolder" => $subFolder));
        $em->remove($doc);
        $em->flush();

        return new JsonResponse(array(
            'index' => $index
        ));
    }

    /**
     * @return DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->container->get('seldocument.manager');
    }
}
