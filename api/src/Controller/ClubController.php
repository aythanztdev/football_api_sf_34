<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Service\ClubService;
use App\Service\FileUploaderService;
use Symfony\Component\DependencyInjection\Tests\Compiler\C;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\SerializerInterface;

class ClubController extends AbstractFOSRestController
{
    private $clubService;
    private $serializer;
    private $fileUploaderService;

    /**
     * ClubController constructor.
     *
     * @param ClubService $clubService
     * @param SerializerInterface $serializer
     * @param FileUploaderService $fileUploaderService
     */
    public function __construct(
        ClubService $clubService,
        SerializerInterface $serializer,
        FileUploaderService $fileUploaderService
    )
    {
        $this->clubService = $clubService;
        $this->serializer = $serializer;
        $this->fileUploaderService = $fileUploaderService;
    }

    /**
     * @return Response
     */
    public function getClubsAction()
    {
        $clubs = $this->clubService->getAll();

        $playersSerialized = $this->serializer->serialize($clubs, 'json', ['groups' => ['club']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Club $club
     *
     * @return Response
     */
    public function getClubAction(Club $club)
    {
        $playersSerialized = $this->serializer->serialize($club, 'json', ['groups' => ['club']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function postClubAction(Request $request)
    {
        $club = new Club();
        $form = $this->clubForm($request, $club);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $shield = $request->files->get('shield');
        if ($shield) {
            $shieldFilename = $this->fileUploaderService->upload($shield);
            $this->clubService->addShield($club, $shieldFilename);
        }

        $this->clubService->persistAndSave($form->getData());

        $playersSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['club']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Club $club
     *
     * @return Response
     */
    public function putClubAction(Request $request, Club $club)
    {
        $form = $this->clubForm($request, $club, true);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->clubService->save();

        $playersSerialized = $this->serializer->serialize($club, 'json', ['groups' => ['club']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Club $club
     *
     * @return Response
     */
    public function patchClubAction(Request $request, Club $club)
    {
        $form = $this->clubForm($request, $club);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->clubService->save();

        $playersSerialized = $this->serializer->serialize($club, 'json', ['groups' => ['club']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param bool $clearMissing
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function clubForm(Request $request, Club $club, $clearMissing = false)
    {
        $form = $this->createForm(ClubType::class, $club);
        $data = array_merge($request->request->all(), $request->files->all());
        $form->submit($data, $clearMissing);

        return $form;
    }
}