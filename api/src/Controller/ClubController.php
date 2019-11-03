<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Service\ClubService;
use App\Service\FileUploaderService;
use App\Service\ValidateService;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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
    private $validateService;

    /**
     * ClubController constructor.
     *
     * @param ClubService $clubService
     * @param SerializerInterface $serializer
     * @param FileUploaderService $fileUploaderService
     * @param ValidateService $validateService
     */
    public function __construct(
        ClubService $clubService,
        SerializerInterface $serializer,
        FileUploaderService $fileUploaderService,
        ValidateService $validateService
    ) {
        $this->clubService = $clubService;
        $this->serializer = $serializer;
        $this->fileUploaderService = $fileUploaderService;
        $this->validateService = $validateService;
    }

    /**
     * @return Response
     */
    public function getClubsAction()
    {
        $clubs = $this->clubService->getAll();

        $clubsSerialized = $this->serializer->serialize($clubs, 'json', ['groups' => ['club']]);
        return new JsonResponse($clubsSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Club $club
     *
     * @return Response
     */
    public function getClubAction(Club $club)
    {
        $clubSerialized = $this->serializer->serialize($club, 'json', ['groups' => ['club']]);
        return new JsonResponse($clubSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Club $club
     *
     * @return Response
     */
    public function getClubPlayersAction(Club $club)
    {
        $playersSerialized = $this->serializer->serialize($club->getPlayers(), 'json', ['groups' => ['clubPlayer']]);
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

        $customErrors = $this->validateService->clubValidation($form->getData());
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->clubService->persistAndSave($form->getData());

        $clubSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['club']]);
        return new JsonResponse($clubSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Club $club
     *
     * @return Response
     */
    public function putClubAction(Request $request, Club $club)
    {
        $form = $this->clubForm($request, $club);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->clubValidation($form->getData());
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->clubService->save();

        $clubSerialized = $this->serializer->serialize($club, 'json', ['groups' => ['club']]);
        return new JsonResponse($clubSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Club $club
     *
     * @return Response
     */
    public function patchClubAction(Request $request, Club $club)
    {
        $form = $this->clubForm($request, $club, false);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->clubValidation($form->getData());
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->clubService->save();

        $clubSerialized = $this->serializer->serialize($club, 'json', ['groups' => ['club']]);
        return new JsonResponse($clubSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Club $club
     * @param bool $clearMissing
     *
     * @return FormInterface
     */
    private function clubForm(Request $request, Club $club, $clearMissing = true)
    {
        $form = $this->createForm(ClubType::class, $club);
        $data = array_merge($request->request->all(), $request->files->all());
        $form->submit($data, $clearMissing);

        return $form;
    }

    /**
     * @param FormInterface $form
     * @param array $errors
     *
     * @return FormInterface
     */
    private function handleErrorsForm(FormInterface $form, array $errors)
    {
        foreach ($errors as $error) {
            $form->addError(new FormError($error));
        }

        return $form;
    }
}
