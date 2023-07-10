<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Cart;
use App\Entity\CartItem;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_add', methods: ['GET', 'POST'])]
    public function addProduct(EntityManagerInterface $em, Request $request): Response
    {
        $productRepository = $em->getRepository(Product::class);

        // Temporary, until we have real authentification
        // We get the first user of BDD to use it as current user
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->findAll()[0];

        $products = $productRepository->findAll();

        // We search if existing user's cart
        $cartRepository = $em->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(array('user' => $user->getId()));

        // If not we create a new one for this user
        if(!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setTotal(0);
            $cart->setCreatedAt(new DateTimeImmutable());
            $cart->setUpdatedAt(new DateTimeImmutable());
        }

        // If method is POST, user is adding one item to cart
        if($request->getMethod() === 'POST') {
            // We get the product id in post params and search the product
            $product_id = $request->get('product-id');
            $product = $productRepository->findOneById($product_id);
            // We search if in this cart, this product is already added
            $cartItemRepository = $em->getRepository(CartItem::class);
            $cartItem = $cartItemRepository->findOneBy(array('product' => $product->getId(), 'cart' => $cart->getId()));
            // If not we create one with quantity 1
            if(!$cartItem) {
                $cartItem = new CartItem();
                $cartItem->setCart($cart);
                $cartItem->setProduct($product);
                $cartItem->setQuantity(1);
                $cartItem->setCreatedAt(new DateTimeImmutable());
            }
            else {
                // Else if it already exists, we add 1 to his quantity
                $cartItem->setQuantity($cartItem->getQuantity() + 1);
            }
    
            $cartItem->setUpdatedAt(new DateTimeImmutable());
            // We update the total of cart with the product price
            $cart->setTotal($cart->getTotal() + $product->getPrice());
            $em->persist($cartItem);
        }
        $em->persist($cart);
        $em->flush();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'cart' => $cart,
            'products'=> $products
        ]);
    }
}
