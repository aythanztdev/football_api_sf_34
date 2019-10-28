<?php

namespace App\Controller;

use App\Entity\Club;
use App\Form\ClubType;
use App\Service\ClubService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClubController extends AbstractFOSRestController
{
    private $clubService;

    public function __construct(ClubService $clubService)
    {
        $this->clubService = $clubService;
    }

    public function postClubAction(Request $request)
    {
        $form = $this->createForm(ClubType::class, new Club());
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->clubService->save($form->getData());

        return $this->handleView($this->view($form, Response::HTTP_CREATED));
    }

    public function getClubAction(Club $club)
    {
        return $this->handleView($this->view($club));
    }

    public function getClubsAction()
    {
        $clubs = $this->clubService->getAll();

        return $this->handleView($this->view($clubs));
    }

    public function putClubAction($slug)
    {}

    public function patchClubAction(Request $request, Club $club)
    {
        $form = $this->createForm(ClubType::class, $club);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->handleView($this->view($form));
        }

        $this->clubService->save($form->getData());

        return $this->handleView($this->view($form, Response::HTTP_OK));
    }
}