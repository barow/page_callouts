<?php

declare(strict_types=1);

namespace Sypets\PageCallouts\Xclass;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\PageLayoutController;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PageLayoutControllerWithCallouts extends PageLayoutController
{
    /**
     * Add flash message in page module via hook.
     *
     * We are using $this->pageinfo (read-only) from parent class. Property is internal
     * so this is a bit ugly - but no better alternative, at the moment.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function generateMessagesForCurrentPage(ServerRequestInterface $request): array
    {
        $content = parent::generateMessagesForCurrentPage($request);
        // added for compatibility with older versions, should use only $this->pageinfo['sys_language_uid'] in future
        $this->pageinfo['lang'] = $this->pageinfo['sys_language_uid'];
        // Add messages via hooks
        foreach (
            $GLOBALS
                ['TYPO3_CONF_VARS']
                ['SC_OPTIONS']
                ['Sypets/PageCallouts/Xclass/PageLayoutControllerWithCallouts']
                ['addFlashMessageToPageModule'] ?? [] as $className
        ) {
            $hook = GeneralUtility::makeInstance($className);
            $result = $hook->addMessages($this->pageinfo);
            if (is_array($result) && !empty($result)) {
                $content[] = $result;
            }
        }
        return $content;
    }
}
