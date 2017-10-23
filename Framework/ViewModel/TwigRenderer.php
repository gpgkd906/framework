<?php
/**
 * PHP version 7
 * File AbstractViewModel.php
 *
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ViewModel;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Extension_Debug;

class TwigRenderer implements RendererInterface
{
    private $_twig;

    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(ROOT_DIR);
        $this->_twig = new Twig_Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
        $this->_twig->addExtension(new Twig_Extension_Debug());
    }
    /**
     * Undocumented function
     *
     * @param ViewModelInterface $ViewModel
     * @return void
     */
    public function render(ViewModelInterface $ViewModel)
    {
        $htmls = [];
        $tpl = str_replace(ROOT_DIR, '', realpath($ViewModel->getTemplateForRender()));
        $template = $this->_twig->load($tpl);
        $data = $ViewModel->getData();
        $data['self'] = $ViewModel;
        $content = $template->render($data);
        return $content;
    }
}
