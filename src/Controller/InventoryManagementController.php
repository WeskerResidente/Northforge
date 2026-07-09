<?php

namespace App\Controller;

use App\Entity\InventoryProduct;
use App\Entity\User;
use App\Repository\InventoryMovementRepository;
use App\Repository\InventoryProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InventoryManagementController extends AbstractController
{
    #[Route('/profil', name: 'app_inventory_management')]
    #[Route('/inventory/management', name: 'app_inventory_management_legacy')]
    public function index(
        InventoryProductRepository $inventoryProductRepository,
        InventoryMovementRepository $inventoryMovementRepository,
    ): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $products = $inventoryProductRepository->findForOwner($user);
        $totalStockValue = array_reduce(
            $products,
            static fn (float $total, $product): float => $total + $product->getStockValue(),
            0.0,
        );
        $totalUnits = array_reduce(
            $products,
            static fn (int $total, $product): int => $total + $product->getQuantity(),
            0,
        );

        return $this->render('inventory_management/inventory_management.html.twig', [
            'createProductUrl' => $this->generateUrl('app_inventory_product_create'),
            'movements' => $inventoryMovementRepository->findCurrentForOwner($user),
            'products' => $products,
            'totalStockValue' => $totalStockValue,
            'totalUnits' => $totalUnits,
            'user' => $user,
        ]);
    }

    #[Route('/profil/produits/ajouter', name: 'app_inventory_product_create', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json(['success' => false, 'message' => 'Vous devez être connecté.'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['success' => false, 'message' => 'Données invalides.'], Response::HTTP_BAD_REQUEST);
        }

        $validation = $this->readProductPayload($data);

        if (!$validation['valid']) {
            return $this->json(['success' => false, 'message' => 'Merci de remplir correctement les champs.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product = (new InventoryProduct())
            ->setOwner($user)
            ->setName($validation['name'])
            ->setDescription($validation['description'])
            ->setCategories([$validation['category']])
            ->setCondition($validation['condition'])
            ->setUnitPrice(number_format($validation['unitPrice'], 2, '.', ''))
            ->setQuantity($validation['quantity'])
        ;

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'product' => $this->formatProduct($product),
            'totals' => $this->getOwnerTotals($entityManager, $user),
        ]);
    }

    #[Route('/profil/produits/{id}/modifier', name: 'app_inventory_product_update', methods: ['POST'])]
    public function updateProduct(
        InventoryProduct $product,
        Request $request,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User || $product->getOwner()?->getId() !== $user->getId()) {
            return $this->json(['success' => false, 'message' => 'Produit introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['success' => false, 'message' => 'Données invalides.'], Response::HTTP_BAD_REQUEST);
        }

        $validation = $this->readProductPayload($data);

        if (!$validation['valid']) {
            return $this->json(['success' => false, 'message' => 'Merci de remplir correctement les champs.'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $product
            ->setName($validation['name'])
            ->setDescription($validation['description'])
            ->setCategories([$validation['category']])
            ->setCondition($validation['condition'])
            ->setUnitPrice(number_format($validation['unitPrice'], 2, '.', ''))
            ->setQuantity($validation['quantity'])
        ;

        $entityManager->flush();

        return $this->json([
            'success' => true,
            'product' => $this->formatProduct($product),
            'totals' => $this->getOwnerTotals($entityManager, $user),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{valid: bool, name: string, description: string, category: string, condition: string, unitPrice: float, quantity: int}
     */
    private function readProductPayload(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $category = trim((string) ($data['category'] ?? ''));
        $condition = trim((string) ($data['condition'] ?? ''));
        $unitPrice = (float) str_replace(',', '.', (string) ($data['unitPrice'] ?? '0'));
        $quantity = (int) ($data['quantity'] ?? 0);

        return [
            'valid' => $name !== '' && $category !== '' && $condition !== '' && $unitPrice >= 0 && $quantity >= 0,
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'condition' => $condition,
            'unitPrice' => $unitPrice,
            'quantity' => $quantity,
        ];
    }

    /**
     * @return array{units: int, stockValue: string, products: int}
     */
    private function getOwnerTotals(EntityManagerInterface $entityManager, User $user): array
    {
        $products = $entityManager->getRepository(InventoryProduct::class)->findBy(['owner' => $user]);
        $totalStockValue = array_reduce(
            $products,
            static fn (float $total, InventoryProduct $product): float => $total + $product->getStockValue(),
            0.0,
        );
        $totalUnits = array_reduce(
            $products,
            static fn (int $total, InventoryProduct $product): int => $total + $product->getQuantity(),
            0,
        );

        return [
            'units' => $totalUnits,
            'stockValue' => $this->formatMoney($totalStockValue),
            'products' => count($products),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function formatProduct(InventoryProduct $product): array
    {
        return [
            'name' => $product->getName(),
            'reference' => $product->getReference(),
            'description' => $product->getDescription(),
            'categories' => $product->getCategories(),
            'condition' => $product->getCondition(),
            'updateUrl' => $this->generateUrl('app_inventory_product_update', ['id' => $product->getId()]),
            'unitPrice' => $product->getUnitPrice(),
            'quantity' => $product->getQuantity(),
            'stockValue' => $this->formatMoney($product->getStockValue()),
            'unitPriceFormatted' => $this->formatMoney((float) $product->getUnitPrice()),
        ];
    }

    private function formatMoney(float $value): string
    {
        return number_format($value, 2, ',', ' ') . ' €';
    }
}
