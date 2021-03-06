<?php

/**
 * Bitrix component iblock.content
 * Компонент для битрикс, работа с инфоблоком одностраничный вывод
 *
 * @author    it-delta
 * @link      https://github.com/it-delta/iblock.content.git
 * @copyright 2020 it-delta.ru
 * @license   GNU General Public License http://www.gnu.org/licenses/gpl-2.0.html
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use CDBResult;
use CIBlockElement;
use CIBlock;

class ItDeltaIblockContentComponent extends CBitrixComponent
{
    private $_request;
    protected $pagination;

    /**
     * Проверка наличия модулей требуемых для работы компонента
     * @return bool
     * @throws Exception
     */
     private function _checkModules() {
        if (   !Loader::includeModule('iblock')
        ) {
            throw new \Exception('Не загружены модули необходимые для работы модуля');
        }

        return true;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllMain|CMain
     */
    private function _app() {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * Обертка над глобальной переменной
     * @return CAllUser|CUser
     */
    private function _user() {
        global $USER;
        return $USER;
    }

    /**
     * Возвращает папрметры сортировки
     *
     * @return array
     */
    protected function getSort()
    {
        $arSort = [
            $this->arParams['SORT_BY1'] => $this->arParams['SORT_ORDER1'],
            $this->arParams['SORT_BY2'] => $this->arParams['SORT_ORDER2'],
        ];

        if ($this->arParams['RAND_ELEMENTS'] == 'Y') {
            $arSort = [
                'RAND' => 'ASC'
            ];
        }

        return $arSort;
    }

    public function onPrepareComponentParams($arParams)
    {
        if (isset($arParams['PAGINATION_NAME'])) {
            $arParams['PAGINATION']['NAME'] = $arParams['PAGINATION_NAME'];
        }

        if (isset($arParams['PAGINATION_TEMPLATE'])) {
            $arParams['PAGINATION']['TEMPLATE'] = $arParams['PAGINATION_TEMPLATE'];
        }

        if (!isset($arParams['ADD_CACHE_STRING'])) {
            $arParams['ADD_CACHE_STRING'] = '';
        }

        if (!isset($arParams['IMG_CACHE'])) {
            $arParams['IMG_CACHE'] = [
                'PREVIEW_PICTURE' => [],
                'DETAIL_PICTURE' => []
            ];

            // if (isset($arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_TYPE'])) {
            //     $arParams['IMG_CACHE']['PREVIEW_PICTURE'] = [
            //         'SIZE' => [
            //             'width' => isset($arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_WIDTH'])
            //                         ? $arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_WIDTH']
            //                         : 800,
            //             'height' => isset($arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_HEIGHT'])
            //                         ? $arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_HEIGHT']
            //                         : 600
            //         ],
            //         'TYPE' => isset($arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_TYPE'])
            //                     ? $arParams['IMG_CACHE_PREVIEW_PICTURE_SIZE_TYPE']
            //                     : BX_RESIZE_IMAGE_EXACT
            //     ];
            // }

            // if (isset($arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_TYPE'])) {
            //     $arParams['IMG_CACHE']['PREVIEW_PICTURE'] = [
            //         'SIZE' => [
            //             'width' => isset($arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_WIDTH'])
            //                         ? $arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_WIDTH']
            //                         : 800,
            //             'height' => isset($arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_HEIGHT'])
            //                         ? $arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_HEIGHT']
            //                         : 600
            //         ],
            //         'TYPE' => isset($arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_TYPE'])
            //                     ? $arParams['IMG_CACHE_DETAIL_PICTURE_SIZE_TYPE']
            //                     : BX_RESIZE_IMAGE_EXACT
            //     ];
            // }
        }

        return $arParams;
    }

    /**
     * Возвращает параметры фильтрации
     *
     * @return array
     */
    protected function getFilter()
    {
        $arFilter = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'IBLOCK_LID' => SITE_ID,
            'IBLOCK_ACTIVE' => 'Y',
            'ACTIVE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R',
        ];

        if ($this->arParams['ACTIVE_DATE'] == 'Y') {
            $arFilter['ACTIVE_DATE'] = 'Y';
        }

        if (!empty($this->arParams['SECTION_ID'])) {
            $arFilter['IBLOCK_SECTION_ID'] = $this->arParams['SECTION_ID'];
        }

        if (!empty($this->arParams["FILTER_NAME"]) && preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"])) {
        	global ${$this->arParams["FILTER_NAME"]};
        	$parsedFilter = ${$this->arParams["FILTER_NAME"]};
        	if (is_array($parsedFilter))
        		$arFilter = array_merge($arFilter, $parsedFilter);
        }

        return $arFilter;
    }

    /**
     * Возвращает параметры количества выбранных записей
     *
     * @return boolean|array
     */
    protected function getPaginationParams()
    {
        if ($this->arParams['PAGE_ELEMENT_COUNT'] == 0) {
            return false;
        }

        return [
            'nPageSize' => $this->arParams['PAGE_ELEMENT_COUNT'],
        ];
    }

    /**
     * Возвращает параметры выборки, т.е. какие поля выбирать
     *
     * @return array
     */
    protected function getSelect()
    {
        return $arSelect = [
            'ID',
            'IBLOCK_ID',
            'CODE',
            'XML_ID',
            'NAME',
            'ACTIVE',
            'DATE_ACTIVE_FROM',
            'DATE_ACTIVE_TO',
            'SORT',
            'PREVIEW_TEXT',
            'PREVIEW_TEXT_TYPE',
            'DETAIL_TEXT',
            'DETAIL_TEXT_TYPE',
            'DATE_CREATE',
            'CREATED_BY',
            'TIMESTAMP_X',
            'MODIFIED_BY',
            'TAGS',
            'IBLOCK_SECTION_ID',
            'DETAIL_PAGE_URL',
            'DETAIL_PICTURE',
            'PREVIEW_PICTURE',
            'SHOW_COUNTER',
            'PROPERTY_*'
        ];
    }

    /**
     * Возвращает результат выборки
     *
     * @return array
     */
    protected function getData()
    {
        $arSort       = $this->getSort();
        $arSelect     = $this->getSelect();
        $arFilter     = $this->getFilter();
        $arPagination = $this->getPaginationParams();

        $arResult = [];
        $arItem   = [];

        $rsElements = CIBlockElement::GetList($arSort, $arFilter, false, $arPagination, $arSelect);

        while ($ob = $rsElements->GetNextElement()) {
            $arItem               = $ob->GetFields();
            $arItem['PROPERTIES'] = $ob->GetProperties();

            $arItem['PREVIEW_PICTURE'] =
                0 < $arItem['PREVIEW_PICTURE']
                ? CFile::GetFileArray($arItem['PREVIEW_PICTURE'])
                : null;

            $arItem['DETAIL_PICTURE'] =
                0 < $arItem['DETAIL_PICTURE']
                ? CFile::GetFileArray($arItem['DETAIL_PICTURE'])
                : null;

            if (isset($this->arParams['IMG_CACHE'])) {
                if (!empty($this->arParams['IMG_CACHE']['PREVIEW_PICTURE'])) {
                     $arItem['PREVIEW_PICTURE_CACHE'] =
                        0 < $arItem['PREVIEW_PICTURE']
                        ? CFile::ResizeImageGet(
                            $arItem['PREVIEW_PICTURE'],
                            $this->arParams['IMG_CACHE']['PREVIEW_PICTURE']['SIZE'],
                            $this->arParams['IMG_CACHE']['PREVIEW_PICTURE']['TYPE']
                        )
                        : null;
                }

                if (!empty($this->arParams['IMG_CACHE']['DETAIL_PICTURE'])) {
                    $arItem['DETAIL_PICTURE_CACHE'] =
                        0 < $arItem['DETAIL_PICTURE']
                        ? CFile::ResizeImageGet(
                            $arItem['DETAIL_PICTURE'],
                            $this->arParams['IMG_CACHE']['DETAIL_PICTURE']['SIZE'],
                            $this->arParams['IMG_CACHE']['DETAIL_PICTURE']['TYPE']
                        )
                        : null;
                }
            }

            $arButtons = CIBlock::GetPanelButtons(
                    $arItem['IBLOCK_ID'], $arItem['ID'], 0,
                    [
                        'SECTION_BUTTONS' => false,
                        'SESSID' => false
                    ]
            );

            $arItem['EDIT_LINK']   = $arButtons['edit']['edit_element']['ACTION_URL'];
            $arItem['DELETE_LINK'] = $arButtons['edit']['delete_element']['ACTION_URL'];

            $arResult[] = $arItem;
        }

        $this->arParams['PAGINATION']['NAME'] =
            (isset($this->arParams['PAGINATION']['NAME']) && !empty($this->arParams['PAGINATION']['NAME']))
            ? $this->arParams['PAGINATION']['NAME']
            : 'Страницы';

        $this->arParams['PAGINATION']['TEMPLATE'] =
            (isset($this->arParams['PAGINATION']['NAME']) && !empty($this->arParams['PAGINATION']['NAME']))
            ? $this->arParams['PAGINATION']['TEMPLATE']
            : '.default';

        $this->pagination = $rsElements->GetPageNavStringEx(
            $navComponentObject, $this->arParams['PAGINATION']['NAME'],
            $this->arParams['PAGINATION']['TEMPLATE']
        );

        return $arResult;
    }

    /**
     * Выполняет компонент
     *
     * @global CMain $APPLICATION
     */
    public function executeComponent()
    {
        $this->_checkModules();
        $this->_request = Application::getInstance()->getContext()->getRequest();

        $pages_count = $this->bitrix->arParams['PAGINATION']['COUNT'] ? : 10;
        $nav         = CDBResult::NavStringForCache($pages_count);
        // дополнительный кеш по адресу страницы + страница навигации + доп. произвольная строка из параметров
        $cache_id    = $this->_app()->GetCurDir() . $nav . $this->arParams['ADD_CACHE_STRING'];

        if ($this->StartResultCache(false, $cache_id)) {
            $this->arResult['ITEMS']      = $this->getData();
            $this->arResult['PAGINATION'] = $this->pagination;
            $this->includeComponentTemplate();
        }
    }
}
