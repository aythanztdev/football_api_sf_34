<?php

namespace App\Controller;

use App\Entity\Coach;
use App\Exception\ServiceNotAvailableException;
use App\Form\CoachType;
use App\Service\CoachService;
use App\Service\NotificationService;
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
    private $notificationService;

    /**
     * CoachController constructor.
     *
     * @param CoachService $coachService
     * @param ValidateService $validateService
     * @param SerializerInterface $serializer
     * @param NotificationService $notificationService
     */
    public function __construct(
        CoachService $coachService,
        ValidateService $validateService,
        SerializerInterface $serializer,
        NotificationService $notificationService
    )
    {
        $this->coachService = $coachService;
        $this->validateService = $validateService;
        $this->serializer = $serializer;
        $this->notificationService = $notificationService;
    }

    /**
     * @return JsonResponse
     */
    public function getCoachesAction()
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
     *
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     * @throws ServiceNotAvailableException
     */
    public function postCoachAction(Request $request)
    {
        $coach = new Coach();
        $form = $this->coachForm($request, $coach);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->coachValidation($coach);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->coachService->unsetLastCoachOnClub($coach);
        $this->coachService->persistAndSave($form->getData());
        $this->notificationService->send($coach, $this->notificationService::TYPE_EMAIL);

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

        $this->coachService->unsetLastCoachOnClub($coach);
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

        $this->coachService->unsetLastCoachOnClub($coach);
        $this->coachService->save();

        $coachsSerialized = $this->serializer->serialize($coach, 'json', ['groups' => ['coach']]);
        return new JsonResponse($coachsSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Coach $coach
     * @param bool $clearMissing
     *
     * @return FormInterface
     */
    private function coachForm(Request $request, Coach $coach, $clearMissing = true)
    {
        $form = $this->createForm(CoachType::class, $coach);
        $form->submit($request->request->all(), $clearMissing);

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