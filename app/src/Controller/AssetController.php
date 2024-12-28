<?php
// src/Controller/AssetController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AssetController extends AbstractController
{
    /**
     * @Route("/asset/{filename}", name="asset_serve")
     */
    public function serveAsset(Request $request, $filename)
    {
        // Sanitize filename
        $filename = basename($filename);
        $path = $this->getParameter('kernel.project_dir') . '/public/img/' . $filename;

        if (file_exists($path) && is_file($path)) {
            return new Response(file_get_contents($path), 200, [
                'Content-Type' => mime_content_type($path),
            ]);
        }

        throw $this->createNotFoundException('Asset not found');
    }
}