<?php


namespace App\Controller;


use App\Entity\Player;
use App\Form\PlayerType;
use App\Service\PlayerService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerController extends AbstractFOSRestController
{
    private $playerService;
    private $serializer;

    public function __construct(
        PlayerService $playerService,
        SerializerInterface $serializer
    )
    {
        $this->playerService = $playerService;
        $this->serializer = $serializer;
    }

    public function getPlayersAction()
    {
        $players = $this->playerService->getAll();

        $playersSerialized = $this->serializer->serialize($players, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    public function getPlayerAction(Player $player)
    {
        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    public function postPlayerAction(Request $request)
    {
        $form = $this->playerForm($request, new Player());

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->playerService->persistAndSave($form->getData());

        $playersSerialized = $this->serializer->serialize($form->getData(), 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_CREATED, [], true);
    }

    public function putPlayerAction(Request $request, Player $player)
    {
        $form = $this->playerForm($request, $player);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->playerService->save();

        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    public function patchPlayerAction(Request $request, Player $player)
    {
        $form = $this->playerForm($request, $player);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->playerService->save();

        $playersSerialized = $this->serializer->serialize($player, 'json', ['groups' => ['player']]);
        return new JsonResponse($playersSerialized, Response::HTTP_OK, [], true);
    }

    private function playerForm(Request $request, Player $player)
    {
        $form = $this->createForm(PlayerType::class, $player);
        $form->submit($request->request->all());

        return $form;
    }
}