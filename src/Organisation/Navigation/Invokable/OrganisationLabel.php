<?php
/**
 * ITEA Office copyright message placeholder.
 *
 * PHP Version 5
 *
 * @category    Organisation
 *
 * @author      Johan van der Heide <johan.van.der.heide@itea3.org>
 * @copyright   2004-2016 ITEA Office
 * @license     https://itea3.org/license.txt proprietary
 *
 * @link        http://github.com/iteaoffice/organisation for the canonical source repository
 */

namespace Organisation\Navigation\Invokable;

use Admin\Navigation\Invokable\AbstractNavigationInvokable;
use Organisation\Entity\Organisation;
use Zend\Navigation\Page\Mvc;

/**
 * Class OrganisationLabel
 *
 * @package Organisation\Navigation\Invokable
 */
class OrganisationLabel extends AbstractNavigationInvokable
{
    /**
     * @param Mvc $page
     *
     * @return void
     */
    public function __invoke(Mvc $page)
    {
        if ($this->getEntities()->containsKey(Organisation::class)) {
            /** @var Organisation $organisation */
            $organisation = $this->getEntities()->get(Organisation::class);
            $page->setParams(array_merge($page->getParams(), [
                'id' => $organisation->getId(),
            ]));
            $label = (string)$organisation;
        } else {
            $label = $this->translate('txt-nav-view');
        }
        $page->set('label', $label);
    }
}
