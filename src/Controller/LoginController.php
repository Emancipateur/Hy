<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
   public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
    {
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}

    /**
     * @Route("/login_success", name="login_success")
     */
    public function postLoginRedirectAction()
    {
        if($this->getUser()->getRoles()[0] == 'ROLE_USER' ){

            return $this->redirectToRoute("app_etablissements_index");
        } else if ($this->getUser()->getRoles()[0] == 'ROLE_GERANT' ) {

         return $this->redirectToRoute("app_etablissements_a");

        } else if ($this->getUser()->getRoles()[0] == 'ROLE_ADMIN' ) {

         return $this->redirectToRoute("app_etablissements_a");

        } else {
            return $this->redirectToRoute("app_etablissements_index");
        }
    }



    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

}