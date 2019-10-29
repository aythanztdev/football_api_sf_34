<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Form\CoachType;
use App\Service\CoachService;
use App\Service\ValidateService;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class CoachController extends AbstractFOSRestController
{
    private $coachService;
    private $validateService;
    private $serializer;

    /**
     * CoachController constructor.
     *
     * @param CoachService $coachService
     * @param ValidateService $validateService
     * @param SerializerInterface $serializer
     */
    public function __construct(
        CoachService $coachService,
        ValidateService $validateService,
        SerializerInterface $serializer
    )
    {
        $this->coachService = $coachService;
        $this->validateService = $validateService;
        $this->serializer = $serializer;
    }

    /**
     * @return JsonResponse
     */
    public function getCoachsAction()
    {
        $coachs = $this->coachService->getAll();

        $coachsSerialized = $this->serializer->serialize($coachs, 'json', ['groups' => ['coach']]);
        return new JsonResponse($coachsSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Coach $coach
     *
     * @return JsonResponse
     */
    public function getCoachAction(Coach $coach)
    {
        $coachsSerialized = $this->serializer->serialize($coach, 'json', ['groups' => ['coach']]);
        return new JsonResponse($coachsSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function postCoachAction(Request $request)
    {
        $form = $this->coachForm($request, new Coach());

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->coachValidation($form->getData());
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->coachService->persistAndSave($form->getData());

        $coachsSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['coach']]);
        return new JsonResponse($coachsSerialized, Response::HTTP_CREATED, [], true);
    }

    /**
     * @param Request $request
     * @param Coach $coach
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function putCoachAction(Request $request, Coach $coach)
    {
        $form = $this->coachForm($request, $coach);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->coachValidation($coach);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->coachService->save();

        $coachsSerialized = $this->serializer->serialize($coach, 'json', ['groups' => ['coach']]);
        return new JsonResponse($coachsSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Coach $coach
     *
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function patchCoachAction(Request $request, Coach $coach)
    {
        $form = $this->coachForm($request, $coach, false);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->coachValidation($coach);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->coachService->save();

        $coachsSerialized = $this->serializer->serialize($coach, 'json', ['groups' => ['coach']]);
        return new JsonResponse($coachsSerialized, Response::HTTP_OK, [], true);
    }

    private function coachForm(Request $request, Coach $coach, $clearMissing = true)
    {
        $form = $this->createForm(CoachType::class, $coach);
        $form->submit($request->request->all(), $clearMissing);

        return $form;
    }

    private function handleErrorsForm(FormInterface $form, array $errors)
    {
        foreach ($errors as $error) {
            $form->addError(new FormError($error));
        }

        return $form;
    }
}