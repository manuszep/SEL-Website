<?php

namespace SelDocumentBundle\Controller;

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
        $em = $this->getDoctrine()->getManager();
        $key = $request->files->keys()[0];

        $document = new Document();
        $media = $request->files->get($key)['documents'][0];

        $subfolder = $request->query->get('subfolder');

        $document->setSubfolder($subfolder);
        $document->setFile($media);

        $em->persist($document);
        $em->flush();

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
        $subFolder = $request->query->get('subfolder');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository("SelDocumentBundle:Document");

        $doc = $repo->findOneBy(array("path" => $path, "subfolder" => $subFolder));
        $em->remove($doc);
        $em->flush();

        return new JsonResponse(array(
            'success' => "Success"
        ));
    }
}
