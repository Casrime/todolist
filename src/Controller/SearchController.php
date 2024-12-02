<?php

declare(strict_types=1);

namespace App\Controller;

use Algolia\SearchBundle\SearchService;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private SearchService $searchService
    )
    {
    }

    #[Route(path: '/search', name: 'search', methods: 'GET')]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $q = $request->query->get('search');
        dump($q);

        $em = $this->managerRegistry->getManagerForClass(Task::class);

        $tasks = $this->searchService->search($em, Task::class, $q);

        dd($tasks);
        return new Response('ok');
    }
}
