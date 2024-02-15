<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface as TranslationTranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadCrumbs extends AbstractExtension
{
    private $urlGenerator;
    private $translator;
    private $session;

    public function __construct(SessionInterface $session,UrlGeneratorInterface $urlGenerator,TranslationTranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->session = $session;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumbs', [$this, 'generateBreadcrumbs'], ['is_safe' => ['html']]),
        ];
    }

    public function generateBreadcrumbs(): string
    {

        try {

              $lang = $this->session->get('_locale') ?? 'es';

        $rutas=[
            'user'=> $this->translator->trans('Teams',[],null,$lang),
            'manager'=> $this->translator->trans('Administrators',[],null,$lang),
        ];

            $currentUrl = explode('/',$_SERVER['REQUEST_URI']);

            if(count($currentUrl) === 3 && $currentUrl[count($currentUrl)-1] == 'home'){
            $breadcrumbs = [
                ['label'=> '<i class="fas fa-house"></i>', 'active'=> true] ,
            ];
            }else{
                if(count($currentUrl) && $currentUrl[count($currentUrl)-1] != ''){
                    if($currentUrl[2] != 'home')
                    $breadcrumbs = [
                        ['label'=> '<i class="fas fa-house"></i>', 'url'=> 'admin_index'] ,
                        ['label'=> $rutas[$currentUrl[2]], 'url'=> 'admin_'.strtolower(str_replace('-','_',$currentUrl[2])).'_index'],
                    ];
                    else
                    $breadcrumbs = [
                        ['label'=> '<i class="fas fa-house"></i>', 'url'=> 'admin_index'] ,
                        ['label'=> $rutas[$currentUrl[2]], 'active'=> true],
                    ];
                } else{
                    $breadcrumbs = [
                        ['label'=> '<i class="fas fa-house"></i>', 'url'=> 'admin_index'] ,
                        ['label'=> $rutas[$currentUrl[2]], 'active'=> true],
                    ];
                }
    
                if(count($currentUrl) === 4 && !empty($currentUrl[3]) && $currentUrl[3] && $currentUrl[3] != 'new')
                    $breadcrumbs[] = ['label'=> $this->translator->trans('Details #%text%', ['%text%' => $currentUrl[3]],null,$lang), 'active'=> true];
                else if(count($currentUrl) === 4 && !empty($currentUrl[3]) && $currentUrl[3])
                    $breadcrumbs[] = ['label'=> $this->translator->trans('New',[],null,$lang), 'active'=> true];
        
                if(count($currentUrl) === 5 && !empty($currentUrl[3]))
                    $breadcrumbs[] = ['label'=> $this->translator->trans('Edit #%text%', ['%text%' => $currentUrl[3]],null,$lang), 'active'=> true];
        
    
            }

            $html = '<nav class="navbar navbar-success bg-light" aria-label="breadcrumb"><ol class="breadcrumb py-2 px-3 m-0">';
            
            foreach ($breadcrumbs as $breadcrumb) {
                $html .= '<li class="breadcrumb-item font-monospace fw-bold '.(isset($breadcrumb['active'])?' active ':'').'" aria-current="page">';
                if (isset($breadcrumb['url'])) {
                    $html .= '<a href="' . $this->urlGenerator->generate($breadcrumb['url']) . '">';
                }
                $html .= $breadcrumb['label'];
                if (isset($breadcrumb['url'])) {
                    $html .= '</a>';
                }
                $html .= '</li>';
            }

            $html .= '</ol></nav>';

        } catch (\Throwable $th) {
            $html='';
        }

        return $html;
    }
}
