<?php
namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private $entityManager;
    private $taskRepository;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
    }

    public function createTask(string $title, string $description, \DateTimeImmutable $dueDate): Task
    {
        $task = new Task();
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setDueDate($dueDate);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function editTask(Task $task, string $title, string $description, \DateTimeImmutable $dueDate): Task
    {
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setDueDate($dueDate);

        $this->entityManager->flush();

        return $task;
    }

    public function deleteTask(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function getAllTasks(): array
    {
        return $this->taskRepository->findAll();
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }
}
