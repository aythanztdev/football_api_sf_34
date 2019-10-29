<?php


namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Service\PlayerService;
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
    private $serializer;

    /**
     * PlayerController constructor.
     *
     * @param PlayerService $playerService
     * @param SerializerInterface $serializer
     */
    public function __construct(
        PlayerService $playerService,
        SerializerInterface $serializer
    )
    {
        $this->playerService = $playerService;
        $this->serializer = $serializer;
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function postPlayerAction(Request $request)
    {
        $form = $this->playerForm($request, new Player());

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->playerService->customValidations($form->getData(), true);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->playerService->persistAndSave($form->getData());

        $playersSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_CREATED, [], true);
    }

    /**
     * @param Request $request
     * @param Player $player
     * @return JsonResponse|Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function putPlayerAction(Request $request, Player $player)
    {
        $form = $this->playerForm($request, $player);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->playerService->customValidations($player);
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function patchPlayerAction(Request $request, Player $player)
    {
        $form = $this->playerForm($request, $player, false);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $customErrors = $this->playerService->customValidations($player);
        if (count($customErrors)) {
            return $this->handleView($this->view($this->handleErrorsForm($form, $customErrors)));
        }

        $this->playerService->save();

        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    public function deletePlayerAction(Player $player)
    {
        $this->playerService->delete($player);

        return new JsonResponse(null, Response::HTTP_OK);
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