<?php

namespace App\Authentication;

use App\Form\LoginType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * Class LoginAuthenticator
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @package App\Authentication
 */
class LoginAuthenticator extends AbstractFormLoginAuthenticator
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var UserRepository */
    protected $userRepo;

    /** @var UserPasswordEncoderInterface */
    protected $passwordEncoder;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var SessionInterface */
    protected $session;

    /**
     * LoginAuthenticator constructor.
     * @param FormFactoryInterface $formFactory
     * @param UserRepository $userRepo
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UrlGeneratorInterface $urlGenerator
     * @param SessionInterface $session
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        UserRepository $userRepo,
        UserPasswordEncoderInterface $passwordEncoder,
        UrlGeneratorInterface $urlGenerator,
        SessionInterface $session
    ) {
        $this->formFactory = $formFactory;
        $this->userRepo = $userRepo;
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->isMethod('POST') && $request->attributes->get('_route') === 'login';
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getCredentials(Request $request)
    {
        $form = $this->formFactory->create(LoginType::class)
            ->handleRequest($request);

        return $form->getData();
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return mixed|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user =
            !is_null($credentials->getUsername())
                ?
                $this->userRepo->loadUserByUsername($credentials->getUsername())
                :
                null
        ;

        if (is_null($user)) {
            throw new CustomUserMessageAuthenticationException('Nom d\'utilisateur ou mot de passe invalide !');
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (
            is_null($credentials->getPassword()) ||
            !$this->passwordEncoder->isPasswordValid($user, $credentials->getPassword())
        ) {
            throw new CustomUserMessageAuthenticationException('Nom d\'utilisateur ou mot de passe invalide !');
        }

        return true;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return RedirectResponse|Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse(
            $this->urlGenerator->generate('homepage')
        );
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('login');
    }
}
