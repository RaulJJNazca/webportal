<?php
/**
 * This file is part of webportal plugin for FacturaScripts.
 * Copyright (C) 2018 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\webportal\Controller;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Core\Lib\ExtendedController;
use FacturaScripts\Plugins\webportal\Lib\WebPortal\UpdateRoutes;

/**
 * Description of EditWebPage.
 *
 * @author Carlos García Gómez
 */
class EditWebPage extends ExtendedController\PanelController
{

    /**
     * Returns basic page attributes.
     *
     * @return array
     */
    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'page';
        $pageData['menu'] = 'web';
        $pageData['showonmenu'] = false;
        $pageData['icon'] = 'fa-globe';

        return $pageData;
    }

    /**
     * Load views.
     */
    protected function createViews()
    {
        $this->addEditView('EditWebPage', 'WebPage', 'page', 'fa-globe');
        $this->addListView('ListWebBlock', 'WebBlock', 'blocks', 'fa-code');
    }

    /**
     * Load data view procedure.
     *
     * @param string $keyView
     * @param ExtendedController\BaseView $view
     */
    protected function loadData($keyView, $view)
    {
        switch ($keyView) {
            case 'EditWebPage':
                $code = $this->request->get('code');
                $view->loadData($code);
                break;

            case 'ListWebBlock':
                $idpage = $this->getViewModelValue('EditWebPage', 'idpage');
                $view->loadData(false, [new DataBaseWhere('idpage', $idpage)], ['ordernum' => 'ASC']);
                break;
        }
    }

    /**
     * Run the actions that alter data before reading it.
     *
     * @param string $action
     *
     * @return bool
     */
    protected function execPreviousAction($action)
    {
        if (!parent::execPreviousAction($action)) {
            return false;
        }

        if ($action === 'save' || $action === 'delete') {
            UpdateRoutes::setRoutes();
        }

        return true;
    }

    /**
     * Run the controller after actions.
     *
     * @param string $action
     */
    protected function execAfterAction($action)
    {
        switch ($action) {
            case 'preview':
                $model = $this->views['EditWebPage']->model;
                if ($model !== false) {
                    $this->response->headers->set('Refresh', '0; ' . $model->url('public'));
                    UpdateRoutes::setRoutes();
                }
                if ($this->user->homepage !== 'PortalHome') {
                    $this->user->homepage = 'PortalHome';
                    $this->user->save();
                }
                break;

            default:
                parent::execAfterAction($action);
        }
    }
}
