<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var AuthenticationUtils */
    protected $authenticationUtils;

    /** @var Environment */
    protected $twig;

    public function __construct(
        FormFactoryInterface $formFactory,
        AuthenticationUtils $authenticationUtils,
        Environment $twig
    ) {
        $this->formFactory = $formFactory;
        $this->authenticationUtils = $authenticationUtils;
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(): Response
    {
        $form = $this->formFactory->create(LoginType::class);

        return new Response(
            $this->twig->render(
                'security/login.html.twig',
                [
                    'last_username' => $this->authenticationUtils->getLastUsername(),
                    'error'         => $this->authenticationUtils->getLastAuthenticationError(),
                    'form' => $form->createView()
                ]
            )
        );
    }
}
