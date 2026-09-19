<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->ensureScope($request, 'products:read');

        $products = Product::query()
            ->when($request->input('search'), function ($query, string $search): void {
                $query->where(function ($productQuery) use ($search): void {
                    $productQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->has('active'), function ($query) use ($request): void {
                $query->where('active', filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN));
            })
            ->latest()
            ->paginate(min((int) $request->input('per_page', 10), 100))
            ->withQueryString();

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->ensureScope($request, 'products:create');

        $product = Product::create($request->validated());

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, Product $product): ProductResource
    {
        $this->ensureScope($request, 'products:read');

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $this->ensureScope($request, 'products:update');

        $product->update($request->validated());

        return new ProductResource($product->refresh());
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->ensureScope($request, 'products:delete');

        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function ensureScope(Request $request, string $scope): void
    {
        abort_unless(
            $request->user()?->tokenCan($scope),
            Response::HTTP_FORBIDDEN,
            'El token no tiene el scope requerido.'
        );
    }
}
