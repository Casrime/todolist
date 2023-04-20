<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends BaseController
{
    public function testCategoryListpageWithLoggedAdmin(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/categories');

        self::assertResponseIsSuccessful();
    }

    public function testCategoryCreatepageWithLoggedAdminWithFakeData(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/categories/create');

        $client->submitForm('Ajouter', [
            'category[title]' => '',
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testCategoryCreatepageWithLoggedAdminWithGoodData(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/categories/create');

        $client->submitForm('Ajouter', [
            'category[title]' => 'A great category',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testCategoryEditpageWithLoggedAdminWithGoodData(): void
    {
        $client = $this->login(BaseController::ADMIN_EMAIL);
        $client->request('GET', '/categories/2/edit');

        $client->submitForm('Modifier', [
            'category[title]' => 'Category two updated',
        ]);

        self::assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }
}
