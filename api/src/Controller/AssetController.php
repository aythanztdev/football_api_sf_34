<?php


namespace App\Controller;


use App\Entity\Asset;
use App\Form\AssetType;
use App\Service\AssetService;
use App\Service\FileUploaderService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class AssetController extends AbstractFOSRestController
{
    private $assetService;
    private $fileUploaderService;
    private $serializer;

    public function __construct(
        AssetService $assetService,
        FileUploaderService $fileUploaderService,
        SerializerInterface $serializer)
    {
        $this->assetService = $assetService;
        $this->fileUploaderService = $fileUploaderService;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function postAssetAction(Request $request)
    {
        $asset = new Asset();
        $form = $this->assetForm($request, $asset);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $shield = $request->files->get('file');
        if ($shield) {
            $assetPath = $this->fileUploaderService->upload($shield);
            $this->assetService->setPath($asset, $request->getSchemeAndHttpHost(), $assetPath);
        }

        $this->assetService->persistAndSave($form->getData());

        $assetSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['asset']]);
        return new JsonResponse($assetSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Asset $asset
     *
     * @return FormInterface
     */
    private function assetForm(Request $request, Asset $asset)
    {
        $form = $this->createForm(AssetType::class, $asset);
        $data = array_merge($request->request->all(), $request->files->all());
        $form->submit($data);

        return $form;
    }
}