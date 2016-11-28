<?php

namespace Application\Navigation;

use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use System\Repository\MenuSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Interop\Container\ContainerInterface;

class MenuNavigation extends DefaultNavigationFactory {

    /**
     * @return string
     */
    private $adapter;
    private $roleId;

    protected function getPages(ContainerInterface $container) {
        if (null === $this->pages) {
            $adapter = $container->get(AdapterInterface::class);
            $this->adapter = $adapter;
            $repository = new MenuSetupRepository($adapter);
            $data = $repository->getHierarchicalMenuWithRoleId();

            foreach ($data as $key => $row) {
                if ($this->menu($row['MENU_ID'])) {
                    $configuration['navigation'][$this->getName()][$row['MENU_NAME']] = array(
                        "label" => $row['MENU_NAME'],
                        "icon" => $row['ICON_CLASS'],
                        'uri' => 'javascript::',
                        "pages" => $this->menu($row['MENU_ID'])
                    );
                } else {
                    $configuration['navigation'][$this->getName()][$row['MENU_NAME']] = array(
                        "label" => $row['MENU_NAME'],
                        "icon" => $row['ICON_CLASS'],
                        "route" => $row['ROUTE'],
                        "action" => $row['ACTION']
                    );
                }
            }

            if (!isset($configuration['navigation'])) {
                throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$this->getName()])) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'Failed to find a navigation container by the name "%s"', $this->getName()
                ));
            }

            $application = $container->get('Application');
            $routeMatch = $application->getMvcEvent()->getRouteMatch();
            $router = $application->getMvcEvent()->getRouter();
            $pages = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);

            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }
        return $this->pages;
    }

    private function menu($parent_menu = null) {
        $menuSetupRepository = new MenuSetupRepository($this->adapter);
        $result = $menuSetupRepository->getHierarchicalMenuWithRoleId($parent_menu);
        $num = count($result);

        if ($num > 0) {
            $temArray = array();
            foreach ($result as $row) {
                $children = $this->menu($row['MENU_ID']);
                if ($children) {
                    $temArray[] = array(
                        "label" => $row['MENU_NAME'],
                        "icon" => $row['ICON_CLASS'],
                        'uri' => 'javascript::',
                        "pages" => $children
                    );
                } else {
                    $temArray[] = array(
                        "label" => $row['MENU_NAME'],
                        "icon" => $row['ICON_CLASS'],
                        "route" => $row['ROUTE'],
                        "action" => $row['ACTION']
                    );
                }
            }
            return $temArray;
        } else {
            return false;
        }
    }

}
