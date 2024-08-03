<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Category;
use App\Exceptions\MethodNotImplementedException;
use App\RequestValidators\CreateCategoryRequestValidator;
use App\RequestValidators\RequestValidatorFactory;
use Doctrine\ORM\EntityManager;
use http\Exception\RuntimeException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class CategoryController
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly Twig $twig,
        private readonly RequestValidatorFactory $requestValidatorFactory,
    ){
    }

    public function form(Request $request, Response $response): Response {
        return $this->twig->render($response, '/forms/createCategory.twig');
    }

    public function fetchAll(Request $request, Response $response) {
        $result = $this->entityManager->getRepository(Category::class)
            ->createQueryBuilder('c')->select('c')->getQuery()->getArrayResult();

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response): Response {
        // get data
        $data = $request->getParsedBody();

        // validate
        $validator = $this->requestValidatorFactory->make(CreateCategoryRequestValidator::class);
        $validator->validate($data);

        // create the category
        $category = new Category();
        $category->setName($data['name']);
        $category->setProductCount(0);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // return response
        return $response->withHeader('Location', '/admin/categories')->withStatus(302);
    }
}