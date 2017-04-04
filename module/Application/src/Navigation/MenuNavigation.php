<?php

namespace Application\Navigation;

use Interop\Container\ContainerInterface;
use System\Repository\MenuSetupRepository;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\View\Exception\InvalidArgumentException;

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
                $tempMenu = $this->menu($row['MENU_ID']);
                if ($tempMenu) {
//                    print "<pre>";
//                    print $row['ROUTE'];
//                    print strpos($row['ROUTE'], 'x');
//                    print strpos($row['ROUTE'], 'javascript') >= 0 ? "t" : "f";
//                    exit;
                    if (strpos($row['ROUTE'], 'javascript') !== FALSE || strpos($row['ACTION'], 'javascript') !== FALSE) {
                        $configuration['navigation'][$this->getName()][$row['MENU_NAME']] = array(
                            "label" => $row['MENU_NAME'],
                            "icon" => $row['ICON_CLASS'],
                            'uri' => 'javascript::',
                            "pages" => $tempMenu['array'],
                            "isVisible" => $row['IS_VISIBLE'],
                            "isChildAllInvisible" => $tempMenu['allInvisible']
                        );
                    } else {
                        $configuration['navigation'][$this->getName()][$row['MENU_NAME']] = array(
                            "label" => $row['MENU_NAME'],
                            "icon" => $row['ICON_CLASS'],
                            "route" => $row['ROUTE'],
                            "action" => $row['ACTION'],
                            "pages" => $tempMenu['array'],
                            "isVisible" => $row['IS_VISIBLE'],
                            "isChildAllInvisible" => $tempMenu['allInvisible']
                        );
                    }
                } else {
                    $configuration['navigation'][$this->getName()][$row['MENU_NAME']] = array(
                        "label" => $row['MENU_NAME'],
                        "icon" => $row['ICON_CLASS'],
                        "route" => $row['ROUTE'],
                        "action" => $row['ACTION'],
                        "isVisible" => $row['IS_VISIBLE']
                    );
                }
            }

            if (!isset($configuration['navigation'])) {
                throw new InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$this->getName()])) {
                throw new InvalidArgumentException(sprintf(
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
            $allInvisible = 0;
            $total = 0;
            foreach ($result as $row) {
                $tempMenu = $this->menu($row['MENU_ID']);
                if (!$tempMenu) {
                    $children = false;
                } else {
                    $children = $tempMenu['array'];
                    $resAllInvisible = $tempMenu['allInvisible'];
                }

                if ($row['IS_VISIBLE'] == 'N') {
                    $allInvisible++;
                }
                $total++;
                if ($children) {
                    if (strpos($row['ROUTE'], 'javascript') !== FALSE || strpos($row['ACTION'], 'javascript') !== FALSE) {
                        $temArray[] = array(
                            "label" => $row['MENU_NAME'],
                            "icon" => $row['ICON_CLASS'],
                            'uri' => 'javascript::',
                            "pages" => $children,
                            "isVisible" => $row['IS_VISIBLE'],
                            "isChildAllInvisible" => $resAllInvisible
                        );
                    } else {
                        $temArray[] = array(
                            "label" => $row['MENU_NAME'],
                            "icon" => $row['ICON_CLASS'],
                            "route" => $row['ROUTE'],
                            "action" => $row['ACTION'],
                            "pages" => $children,
                            "isVisible" => $row['IS_VISIBLE'],
                            "isChildAllInvisible" => $resAllInvisible
                        );
                    }
                } else {
                    $temArray[] = array(
                        "label" => $row['MENU_NAME'],
                        "icon" => $row['ICON_CLASS'],
                        "route" => $row['ROUTE'],
                        "action" => $row['ACTION'],
                        "isVisible" => $row['IS_VISIBLE']
                    );
                }
            }
//            return $temArray;
            return ['array' => $temArray, 'allInvisible' => ($allInvisible == $total)];
        } else {
            return false;
        }
    }

}
