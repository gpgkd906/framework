<?php
/**
 * PHP version 7
 * File AbstractViewModel.php
 *
 * @category Module
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\ViewModel;

use Closure;

class Renderer implements RendererInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    /**
     * Undocumented function
     *
     * @param ViewModelInterface $ViewModel
     * @return void
     */
    public function render(ViewModelInterface $ViewModel)
    {
        $content = null;
        $template = $ViewModel->getTemplateForRender();
        $data = $ViewModel->getViewModelManager()->escapeHtml($ViewModel->getData());
        $ViewModel->setData($data);
        ob_start();
        echo '<!-- ' . $template . ' start render-->', PHP_EOL;
        is_array($data) && extract($data);
        $self = $ViewModel;
        include $template;
        echo '<!-- ' . $template . ' end render-->';
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
