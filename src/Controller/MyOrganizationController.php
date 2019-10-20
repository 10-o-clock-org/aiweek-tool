<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\OrganizationDetailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyOrganizationController extends AbstractController
{
    /**
     * @Route("/my-organization", name="my_organization")
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->ensureEditableOrganizationDetails();

        $form = $this->createForm(OrganizationDetailType::class, $user->getProposedOrganizationDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form['logo']->getData();

            if ($logoFile) {
                $user->getProposedOrganizationDetails()->setLogoBlob(file_get_contents($logoFile->getPathname()));
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Die Änderungen wurden gespeichert und zum Review eingereicht.'
            );
        }

        return $this->render('my_organization/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
