<?php
/**
 * @copyright Copyright (c) 2014 Orange Applications for Business
 * @link      http://github.com/kambalabs for the sources repositories
 *
 * This file is part of Kamba.
 *
 * Kamba is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Kamba is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kamba.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace KmbMcollective\View\Decorator;

use GtnDataTables\View\AbstractDecorator;
use KmbMcollective\Model\McollectiveLogInterface;

class ReplyDecorator extends AbstractDecorator
{
    /**
     * @return string
     */
    public function decorateTitle()
    {
        return $this->translate('Replies');
    }

    /**
     * @param McollectiveLogInterface $object
     * @return string
     */
    public function decorateValue($object,$context = null)
    {
        $value = '<a href="'.$this->url('mcollective_show',['action' => 'showDetail','id' => $object->getId()],[],true) .'">';
        $status = $object->getGlobalStatus()['status'];
        $label = '';
        switch($status){
            case 'error':
                $label='danger';
                break;
            case 'partial':
                $label='warning';
                break;
            case 'success':
                $label='success';
                break;
            default:
                $label='default';
                break;
        }
        return $value . '<span class="label label-'.$label.'" >'. sprintf($this->translatePlural("%d server","%d servers",$object->getServerReplyCount()),$object->getServerReplyCount()).'</a>';
    }
}
