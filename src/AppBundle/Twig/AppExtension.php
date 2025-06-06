<?php

namespace App\AppBundle\Twig;

use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Markup;

class AppExtension extends AbstractExtension
{

    protected string $projectDirectory;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDirectory = $kernel->getProjectDir();
    }

    public function getFunctions(): array
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

    /**
     * Loads a component.
     *
     * @param string $componentName The name of the component
     * @param array<string, mixed> $data The data to pass to the component
     * @return Markup The rendered component
     */
    public function loadComponent(
        string $componentName, 
        array $data = []
    ) : Markup {
        
        $jsonEncodedData = json_encode($data);
        if ($jsonEncodedData === false) {
            throw new \RuntimeException('Failed to encode data to JSON.');
        }

        $parameters = base64_encode($jsonEncodedData);

        return new Markup('<div class="react-component" data-component="'
            .$componentName
            .'" data-parameters="'
            .$parameters.'"></div>', 'UTF-8');
    }

    public function loadStyles() : Markup
    {
        $styles= '';
        $files = scandir($this->projectDirectory.'/public/assets');
        if ($files !== false) {
            foreach ($files as $file){
                if(str_starts_with($file, 'main.') && str_ends_with($file, '.css')){
                    $styles .= '<link rel="stylesheet" href="/assets/'.$file.'">';
                }
            }
        }
        return new Markup($styles, 'UTF-8');
    }

    public function loadScripts() : Markup
    {
        $scripts = '';
        $files = scandir($this->projectDirectory.'/public/assets');
        if ($files !== false) {
            foreach ($files as $file){
                if ($file !== '.' && $file != '..') {
                    $scripts .= '<script src="/assets/'.$file.'"></script>';
                }
            }
        }
        return new Markup($scripts, 'UTF-8');
    }
}