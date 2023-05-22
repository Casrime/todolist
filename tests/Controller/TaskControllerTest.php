<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends BaseController
{
    public function testTasksPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks');

        self::assertResponseIsSuccessful();
    }

    public function testFinishedPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/finished');

        self::assertResponseIsSuccessful();
    }

    public function testShowPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/show');

        self::assertResponseIsSuccessful();
    }

    public function testTaskPage(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/edit');

        self::assertResponseIsSuccessful();
    }

    public function testCreateTaskPageWithFakeData(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/create');

        self::assertSame('Créer un utilisateur', $this->trans('app.user.create'));
        self::assertSame('Créer une catégorie', $this->trans('app.category.create'));
        self::assertSame('Se déconnecter', $this->trans('app.logout'));
        self::assertSame('Titre', $this->trans('app.title'));
        self::assertSame('Contenu', $this->trans('app.content'));
        self::assertSame('Catégorie', $this->trans('app.category'));
        self::assertSame('Ajouter', $this->trans('app.add'));
        self::assertSame('Retour à la liste des tâches', $this->trans('app.back.to.task.list'));

        $client->submitForm($this->trans('app.add'), [
            'task[title]' => '',
            'task[content]' => '',
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame('Vous devez saisir un titre.', $this->trans('app.task.title.not.blank', [], 'validators'));
        self::assertSame('Vous devez saisir du contenu.', $this->trans('app.task.content.not.blank', [], 'validators'));
        self::assertSame('Vous devez sélectionner une catégorie.', $this->trans('app.task.category.not.blank', [], 'validators'));
    }

    public function testCreateTaskPageWithGoodData(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/create');

        $client->submitForm($this->trans('app.add'), [
            'task[title]' => 'a new task',
            'task[content]' => 'a new content',
            'task[category]' => 1,
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testEditTaskPageWithGoodData(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/edit');

        $client->submitForm($this->trans('app.edit'), [
            'task[title]' => 'task updated',
            'task[content]' => 'content updated',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testToggleTask(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/toggle');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        /** @var Task $task */
        $task = $this->getIdentifier(TaskRepository::class)->find(1);
        self::assertSame('La tâche '.$task->getTitle().' a bien été marquée comme à réaliser.', $this->trans('app.task.toggle.not.done', ['%taskTitle%' => $task->getTitle()]));
    }

    public function testDeleteTask(): void
    {
        $client = $this->login(BaseController::USER_EMAIL);
        $client->request('GET', '/tasks/1/delete');

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskOtherUser(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/tasks/1/delete');

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
