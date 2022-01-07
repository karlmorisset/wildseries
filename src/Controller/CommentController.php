<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/{id}", name="comment_delete", methods={"POST"})
     */
    public function delete(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        $epidode = $comment->getEpisode()->getSlug();

        if(($this->getUser() == $comment->getAuthor()) || in_array("ROLE_ADMIN",$this->getUser()->getRoles())){
            if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
                $em->remove($comment);
                $em->flush();

                $this->addFlash("danger", "comment.deleted");
            }
        }

        return $this->redirectToRoute('episode_show', ["episode" => $epidode], Response::HTTP_SEE_OTHER);
    }
}
