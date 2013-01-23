<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssortmentBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Prototype controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class PrototypeController extends ResourceController
{
    /**
     * Build a product from the given prototype.
     * Everything else works exactly like in product
     * creation action.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function buildAction(Request $request, $id)
    {
        $prototype = $this->findOr404(array('id' => $id));
        $productController = $this->getProductController();

        $product = $productController->createNew();

        $this
            ->getBuilder()
            ->build($prototype, $product)
        ;

        $form = $productController->getForm($product);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $manager = $productController->getManager();

            $manager->persist($product);
            $manager->flush();

            return $productController->redirectTo($product);
        }

        return $productController->renderResponse('create.html', array(
            'prototype' => $prototype,
            'product'   => $product,
            'form'      => $form->createView()
        ));
    }

    /**
     * Get product controller.
     *
     * @return Controller
     */
    protected function getProductController()
    {
        return $this->get('sylius_assortment.controller.product');
    }

    /**
     * Get prototype builder.
     *
     * @return PrototypeBuilderInterface
     */
    protected function getBuilder()
    {
        return $this->getService('builder');
    }
}