<?php
// ########################################################################
// # File: $Id: openFeeds.php 217 2009-08-14 13:56:19Z jens $
// ########################################################################
// # This program is free software; you can redistribute it and/or modify
// # it under the terms of the GNU General Public License V3
// #
// # This program is subject to the GPL license, that is bundled with
// # this package in the file /share/LICENSE.TXT.
// # If you did not receive a copy of the GNU General Public License
// # along with this program write to the Free Software Foundation, Inc.,
// # 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
// #
// # This program is distributed in the hope that it will be useful,
// # but WITHOUT ANY WARRANTY; without even the implied warranty of
// # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// # GNU General Public License for more details.
// #
// ########################################################################
// # Autor: $Author: jens $
// ########################################################################
// # Revision: $Revision: 217 $
// ########################################################################
/**
* openFeeds
*
* Part of the openWebX-API
* This class is stable
* @author Jens Reinemuth <jens@openos.de>
* @version $Id: openFeeds.php 217 2009-08-14 13:56:19Z jens $
* @package openWebX
* @subpackage openFeeds
* @uses openWebX
*/
class openFeeds extends openWebX implements openObject {

    public $data = array();

    public function __construct() {
    	$this->registerSlots();
    }

    public function __destruct() {

    }

    public function handleSignal($strSignalName,$mixedParams) {
        $this->feedsType = $mixedParams[0];
        $this->feedsBuildHeader();
        $this->feedsBuildFooter();
    }

    private function feedsBuildHeader() {
    	switch (strtolower($this->data['feedsType'])) {
    		case 'rss':
                $this->feedsBuildRSSHeader();
                break;
            case 'atom':
                $this->feedsBuildAtomHeader();
                break;
    	}
    }

    private function feedsBuildRSSHeader() {
        $this->feedsHeader = '
        <?xml version="1.0" encoding="utf-8"?>
        <rss xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">
        <channel>
        ';
    }

    private function feedsBuildAtomHeader() {
        $this->feedsHeader = '
        <?xml version="1.0" encoding="utf-8"?>
        <feed xmlns="http://www.w3.org/2005/Atom">
        ';
    }

    private function feedsBuildFooter() {
        switch (strtolower($this->data['feedsType'])) {
            case 'rss':
                $this->feedsBuildRSSFooter();
                break;
            case 'atom':
                $this->feedsBuildAtomFooter();
                break;
        }
    }

    private function feedsBuildRSSFooter() {
        $this->feedsHeader = '
        </channel>
        ';
    }

    private function feedsBuildAtomFooter() {
        $this->feedsHeader = '
        </feed>
        ';
    }

    private function registerSlots() {
        openWebX::registerSlot($this,'feed',0);
    }
}