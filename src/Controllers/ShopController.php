<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

/**
 * Public-facing shop — product listing and product detail pages.
 */
class ShopController extends BaseController
{
    private Product  $productModel;
    private Category $categoryModel;

    public function __construct(\App\Http\Request $request)
    {
        parent::__construct($request);
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
    }

    /**
     * Display the product catalogue, optionally filtered by category.
     *
     * @return void
     */
    public function index(): void
    {
        $categoryId       = $this->request->query('category') !== null
            ? (int) $this->request->query('category')
            : null;

        $activeProductList = $this->productModel->getActiveProducts($categoryId);
        $allCategoryList   = $this->categoryModel->getAllSorted();

        $this->render('shop/index', [
            'pageTitle'        => 'Shop',
            'activeProductList'=> $activeProductList,
            'allCategoryList'  => $allCategoryList,
            'activeCategoryId' => $categoryId,
        ]);
    }

    /**
     * Display a single product detail page.
     *
     * @param  string  $slug  Product slug from the URL.
     *
     * @return void
     */
    public function show(string $slug): void
    {
        $product = $this->productModel->findBySlug($slug);

        if ($product === null || (int) $product['is_active'] === 0) {
            http_response_code(404);
            echo '<h1>Product not found.</h1>';
            return;
        }

        $this->render('shop/product', [
            'pageTitle' => $product['name'],
            'product'   => $product,
        ]);
    }
}
