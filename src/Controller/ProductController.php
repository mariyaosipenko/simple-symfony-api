<?php


namespace App\Controller;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/add", name="add_product", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $sku = $data['sku'];
        $category = $data['category'];
        $brand = $data['brand'];
        if (empty($name) || empty($sku) || empty($category) || empty($brand)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->productRepository->saveProduct($name, $sku, $category, $brand);
        return new JsonResponse(['status' => 'Product created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_product", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getOneProduct($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
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
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $filters = $data['filters'] ?? [];
        $result = $this->productRepository->findProducts($page, $limit, $filters);
        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * @Route("/change_status/{id}", name="change_status_product", methods={"PUT"})
     * @param $id
     * @return JsonResponse
     */
    public function changeStatus($id): JsonResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);
        if (!$product) {
            return new JsonResponse(['status' => 'product not found'], Response::HTTP_NOT_FOUND);
        }
        $product->changeStatus();
        $this->productRepository->updateProduct($product);
        return new JsonResponse(['status' => 'product updated!'], Response::HTTP_OK);
    }


}