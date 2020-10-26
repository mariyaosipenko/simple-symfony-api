<?php


namespace App\Controller;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Class ProductController
 * @package App\Controller
 *
 * @Route(path="/api")
 */
class ProductController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    /**
     * @Route("/get/{id}", name="get_one_product", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getOneProduct($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => (int)$id]);
        if (!$product) {
            return new JsonResponse(['status' => 'product not found'], Response::HTTP_NOT_FOUND);
        }
        $data = $product->toArray();
        return new JsonResponse(['product' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_products", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllProducts(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $validator = Validation::createValidator();
        $constraint = new Assert\Collection([
            'Product' => new Assert\Collection([
                'id' => new Assert\Choice( [new Assert\Type(['type' => 'integer']), '']),
                'name' => new Assert\Choice( [new Assert\Type(['type' => 'string']), '']),
                'sku' => new Assert\Choice( [new Assert\Type(['type' => 'string']), '']),
                'category' => new Assert\Choice( [new Assert\Type(['type' => 'string']), '']),
                'brand' => new Assert\Choice( [new Assert\Type(['type' => 'string']), '']),
                'stock' => new Assert\Choice( [new Assert\Type(['type' => 'integer']), '']),
                'price' => new Assert\Choice( [new Assert\Type(['type' => 'float']), '']),
                'discountPrice' => new Assert\Choice( [new Assert\Type(['type' => 'float']), '']),
                'status' =>  new Assert\Choice(['inactive', 'active', '']),
            ]),
            'Attribute'=> new Assert\Optional([
                new Assert\Type('array'),
                new Assert\All([
                    new Assert\Collection([
                        'attributeKey' => [
                            new Assert\NotBlank(),
                            new Assert\Type(['type' => 'string'])
                        ],
                        'attributeValue' => [
                            new Assert\NotBlank(),
                            new Assert\Type(['type' => 'string'])
                        ],
                    ]),
                ]),
            ]),
            'Pagination'=> new Assert\Collection([
                'per_page' => new Assert\Type(['type' => 'integer']),
                'page' => new Assert\Type(['type' => 'integer']),
            ]),
        ]);
        $violations = $validator->validate($data, $constraint);
        if (0 === count($violations)) {
            $result = $this->productRepository->findProducts($data['Product'], $data['Attribute'], $data['Pagination']);
            return new JsonResponse($result, Response::HTTP_OK);
        } else {
            $errorMessage = $violations[0]->getMessage();
            foreach ($violations as $violation){
                $errorMessage .= $violation->getMessage() . PHP_EOL;
            }
            return new JsonResponse($errorMessage, Response::HTTP_I_AM_A_TEAPOT);
        }
    }

    /**
     * @Route("/change_status/{id}", name="change_status_product", methods={"PUT"})
     * @param $id
     * @return JsonResponse
     */
    public function changeStatus($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => (int)$id]);
        if (!$product) {
            return new JsonResponse(['status' => 'product not found'], Response::HTTP_NOT_FOUND);
        }
        $product->changeStatus();
        $this->productRepository->updateProduct($product);
        return new JsonResponse(['status' => 'product updated!'], Response::HTTP_OK);
    }


}