<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TaskController
{

    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var FlashBagInterface */
    protected $flash;

    /** @var Environment */
    protected $twig;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var Security */
    protected $security;

    /**
     * UserController constructor.
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $em
     * @param FlashBagInterface $flash
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param Security $security
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        FlashBagInterface $flash,
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        Security $security
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->flash = $flash;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    /**
     * @Route("/tasks", name="task_list")
     * @param TaskRepository $taskRepository
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(TaskRepository $taskRepository)
    {
        return new Response($this->twig->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]));
    }

    /**
     * @Route("/tasks/create", name="task_create")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create(Request $request)
    {
        $form = $this->formFactory->create(TaskType::class, $task = new Task())
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->security->getUser());
            $this->em->persist($task);
            $this->em->flush();
            $this->flash->add('success', 'La tâche a été bien été ajoutée.');

            return new RedirectResponse($this->urlGenerator->generate('task_list'));
        }

        return new Response($this->twig->render('task/create.html.twig', ['form' => $form->createView()]));
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     * @param Task $task
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Task $task, Request $request)
    {
        $form = $this->formFactory->create(TaskType::class, $task)
                                  ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->flash->add('success', 'La tâche a bien été modifiée.');

            return new RedirectResponse($this->urlGenerator->generate('task_list'));
        }

        return new Response(
            $this->twig->render(
                'task/edit.html.twig',
                [
                    'form' => $form->createView(),
                    'task' => $task
                ]
            )
        );
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     * @param Task $task
     * @return RedirectResponse
     */
    public function toggleTask(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();
        $this->flash->add('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @param Task $task
     * @return RedirectResponse
     */
    public function deleteTask(Task $task)
    {
        $this->em->remove($task);
        $this->em->flush();
        $this->flash->add('danger', 'La tâche a bien été supprimée.');

        return new RedirectResponse($this->urlGenerator->generate('task_list'));
    }
}
