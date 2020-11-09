# iblock.content

Универсальный компонент инфоблоков для CMS Bitrix (замена news.list).
Вывод одностраничниго контента

```php
$APPLICATION->IncludeComponent(
	"falur:iblock.content",
	"",
	array(
		"IBLOCK_TYPE" => "news",
		"IBLOCK_ID" => "1",
		"SORT_BY1" => "DATE_ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "ID",
		"SORT_ORDER2" => "DESC",
		"FILTER_NAME" => '',					// имя переменной с фильтром
		"PAGE_ELEMENT_COUNT" => "4",
		"RAND_ELEMENTS" => "N",				// вывод в случайном порядке (сортировка не будет работать)
		"CACHE_TYPE" => "A",
    "CACHE_TIME" => 3600,
    // Не обязательные параметры
    "PAGINATION" => [
        "NAME" => "Страницы",
        "TEMPLATE" => ".default"
    ],
    "IMG_CACHE" => [
        "PREVIEW_PICTURE" => [
            "SIZE" => ["width" => 200, "height" => 200],
            "TYPE" => BX_RESIZE_IMAGE_EXACT
        ],
        "DETAIL_PICTURE" => [
            "SIZE" => ["width" => 200, "height" => 200],
            "TYPE" => BX_RESIZE_IMAGE_EXACT
        ]
    ],
		"ADD_CACHE_STRING" = "",	// дополнительная строка для кеширования
	)
);
```
