<?php
namespace App\Controller;
use App\Entity\Category;
use App\Entity\Program;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/wild", name="wild_")
 * @ORM\Entity
 * @ORM\Table(name="wild_controller")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()->getRepository(Program::class)->findAll();
        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
        ]);
    }
    /**
     * @Route("/show/{slug}", requirements={"slug"="[a-z0-9-]+"},
     *     defaults={"slug"=null},
     *     name="show")
     * @param $slug
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace('/-/', ' ', ucwords(trim(strip_tags($slug)), "-"));
        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException('No program with ' . $slug . ' title, found in program\'s table.');
        }
        return $this->render('wild/show.html.twig', [
            'slug' => $slug,
            'program' => $program,
        ]);
    }
    /**
     * @Route("/category/{categoryName}", requirements={"categoryName"="[a-z0-9-]+"},
     *     defaults={"categoryName"=null},
     *     name="show_category")
     * @param string|null $categoryName
     * @return Response
     */
    public function showByCategory(?string $categoryName): Response
    {
        if (!$categoryName) {
            throw $this->createNotFoundException('No category has been sent to find a category in category\'s table.');
        }
        $categoryName = ucwords($categoryName);
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
        if (!$category) {
            throw $this->createNotFoundException('No program with the category ' . $categoryName . ', found in category\'s table.');
        }
        $shows = $this->getDoctrine()->getRepository(Program::class)->findBy(['category' => $category], ['id' => 'DESC'], 3, null);
        return $this->render('wild/category.html.twig', [
            'shows' => $shows,
        ]);
    }
}