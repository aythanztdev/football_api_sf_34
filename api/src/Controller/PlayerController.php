<?php


namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Service\NotificationService;
use App\Service\PlayerService;
use App\Service\ValidateService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerController extends AbstractFOSRestController
{
    private $playerService;
    private $validateService;
    private $serializer;
    private $notificationService;

    /**
     * PlayerController constructor.
     *
     * @param PlayerService $playerService
     * @param ValidateService $validateService
     * @param SerializerInterface $serializer
     * @param NotificationService $notificationService
     */
    public function __construct(
        PlayerService $playerService,
        ValidateService $validateService,
        SerializerInterface $serializer,
        NotificationService $notificationService
    )
    {
        $this->playerService = $playerService;
        $this->validateService = $validateService;
        $this->serializer = $serializer;
        $this->notificationService = $notificationService;
    }

    /**
     * @return JsonResponse
     */
    public function getPlayersAction()
    {
        $players = $this->playerService->getAll();

        $playersSerialized = $this->serializer->serialize($players, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Player $player
     *
     * @return JsonResponse
     */
    public function getPlayerAction(Player $player)
    {
        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     * @throws \App\Exception\ServiceNotAvailable
     */
    public function postPlayerAction(Request $request)
    {
        $form = $this->playerForm($request, new Player());

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->playerValidation($form->getData());
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->playerService->persistAndSave($form->getData());
        $this->notificationService->send($form->getData(), $this->notificationService::TYPE_EMAIL);

        $playersSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_CREATED, [], true);
    }

    /**
     * @param Request $request
     * @param Player $player
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function putPlayerAction(Request $request, Player $player)
    {
        $lastClub = $player->getClub();
        $form = $this->playerForm($request, $player);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->playerValidation($player, $lastClub);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->playerService->save();

        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Request $request
     * @param Player $player
     *
     * @return JsonResponse|Response
     *
     * @throws NonUniqueResultException
     */
    public function patchPlayerAction(Request $request, Player $player)
    {
        $lastClub = $player->getClub();
        $form = $this->playerForm($request, $player, false);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->validateService->playerValidation($player, $lastClub);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->playerService->save();

        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    /**
     * @param Player $player
     *
     * @return JsonResponse
     */
    public function deletePlayerAction(Player $player)
    {
        $this->playerService->delete($player);

        return new JsonResponse('', Response::HTTP_OK, [], true);
    }

    private function playerForm(Request $request, Player $player, $clearMissing = true)
    {
        $form = $this->createForm(PlayerType::class, $player);
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