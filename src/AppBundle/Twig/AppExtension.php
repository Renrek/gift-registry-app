<?php

namespace App\AppBundle\Twig;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Markup;

class AppExtension extends AbstractExtension
{

    protected $projectDirectory;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDirectory = $kernel->getProjectDir();
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'loadComponent', 
                [$this, 'loadComponent'], 
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'loadStyles', 
                [$this, 'loadStyles'], 
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'loadScripts', 
                [$this, 'loadScripts'], 
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function loadComponent(
        string $componentName, 
        array $data = []
    ) : string {
        
        $parameters = base64_encode(json_encode($data));

        return new Markup('<div class="react-component" data-component="'
            .$componentName
            .'" data-parameters="'
            .$parameters.'"></div>', 'UTF-8');
    }

    public function loadStyles() : string
    {
        $styles= '';
        $files = scandir($this->projectDirectory.'/public/assets');
        foreach ($files as $file){
            if(str_starts_with($file, 'main.') && str_ends_with($file, '.css')){
                $styles .= '<link rel="stylesheet" href="/assets/'.$file.'">';
            }
        }
        return new Markup($styles, 'UTF-8');
    }

    public function loadScripts() : string
    {
        $scripts = '';
        $files = scandir($this->projectDirectory.'/public/assets');
        foreach ($files as $file){
            if(str_starts_with($file, 'main.') && str_ends_with($file, '.js')){
                $scripts .= '<script src="/assets/'.$file.'"></script>';
            }
        }
        return new Markup($scripts, 'UTF-8');
    }
}