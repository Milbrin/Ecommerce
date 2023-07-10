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
        $userRepository = $em->getRepository(User::class);
        $user = $userRepository->findAll()[0];

        $products = $productRepository->findAll();

        $cartRepository = $em->getRepository(Cart::class);
        $cart = $cartRepository->findOneBy(array('user' => $user->getId()));
        if(!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setCreatedAt(new DateTimeImmutable());
            $cart->setUpdatedAt(new DateTimeImmutable());
        }

        if($request->getMethod() === 'POST') {
            $product_id = $request->get('product-id');
            $product = $productRepository->findOneById($product_id);
            $cartItemRepository = $em->getRepository(CartItem::class);
            $cartItem = $cartItemRepository->findOneBy(array('product' => $product->getId(), 'cart' => $cart->getId()));
            if(!$cartItem) {
                $cartItem = new CartItem();
                $cartItem->setCart($cart);
                $cartItem->setProduct($product);
                $cartItem->setQuantity(1);
                $cartItem->setCreatedAt(new DateTimeImmutable());
            }
            else {
                $cartItem->setQuantity($cartItem->getQuantity() + 1);
            }
    
            $cartItem->setUpdatedAt(new DateTimeImmutable());
            $cart->setTotal($cartItem->getQuantity() * $product->getPrice());
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
