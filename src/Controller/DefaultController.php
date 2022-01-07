<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(Request $request): Response
    {
        return $this->render('index.html.twig');
    }


    /**
     * @Route("/lang", name="app_change_lang")
     */
    public function changeLang(Request $request): Response
    {
        $url = parse_url($request->headers->get('referer'))['path'];
        $re = '/([a-z]{2})\/(.*)/m';
        preg_match($re, $url, $matches);

        $referer = sprintf("/%s/%s",$request->query->get('lang'), $matches[2]);

        return $this->redirect($referer);
    }


    /**
     * Permet de rendre une vue spécifique pour une dropdown des catégories
     *
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function navbarTop(CategoryRepository $categoryRepository): Response
    {
       return $this->render('layout/_navbartop.html.twig', [
          'categories' => $categoryRepository->findBy([], ['id' => 'DESC'])
       ]);
    }

}
