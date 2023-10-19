<?php

namespace App\Controller;
date_default_timezone_set('Europe/Bucharest');
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\TaskType;

class ToDoController extends AbstractController
{
    
    #[Route('/list', name: 'app_list')]
    public function list(Request $request, TaskRepository $taskRepository, UrlGeneratorInterface $urlGenerator): Response
    {
        $page = $request->query->getInt('page', 1); // Obține numărul paginii din URL
        $itemsPerPage = 10; // Numărul de sarcini pe pagină
        $tasks = $taskRepository->findAll(); // Obține toate sarcinile

        // Calculează numărul total de pagini
        $totalPages = ceil(count($tasks) / $itemsPerPage);

        // Pagina curentă nu poate fi mai mare decât numărul total de pagini
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        // Calculează indicele de start și de final pentru afișarea sarcinilor curente
        $startIndex = ($page - 1) * $itemsPerPage;
        $endIndex = $startIndex + $itemsPerPage;

        // Obține doar sarcinile pentru pagina curentă
        $tasksOnPage = array_slice($tasks, $startIndex, $itemsPerPage);

        return $this->render('to_do/list.html.twig', [
            'tasks' => $tasksOnPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'urlGenerator' => $urlGenerator,
        ]);
    }

    #[Route('/view/{id}', name: 'app_view')]
    public function view(int $id, CategoryRepository $taskRepository): Response
    {
        $task = $taskRepository->find($id);
        if ($task === null) {
            throw $this->createNotFoundException('Task not found');
        }
        return $this->render('to_do/view.html.twig', ['task' => $task]);
    }


    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        // Verificăm dacă există o cerere POST pentru confirmarea ștergerii
        if ($request->isMethod('POST')) {
            // Ștergem sarcina din baza de date
            $entityManager->remove($task);
            $entityManager->flush();

            // Redirecționăm către pagina de listare a sarcinilor sau altă pagină
            return $this->redirectToRoute('app_list');
        }

        // Afisăm un formular de confirmare a ștergerii
        return $this->render('to_do/delete.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/create', name: 'app_create')]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Salvăm sau actualizăm sarcina în baza de date
            $entityManager->persist($task);
            $entityManager->flush();

            // Redirecționăm către pagina de listare a sarcinilor sau altă pagină
            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/update/{id}', name: 'app_update')]
    public function update(Request $request, Task $task, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Actualizăm sarcina în baza de date
            $entityManager->flush();

            // Redirecționăm către pagina de listare a sarcinilor sau altă pagină
            return $this->redirectToRoute('app_list');
        }

        return $this->render('to_do/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
