<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
?>

<div class="i-review">
    <ul>
<? foreach ($arResult['ITEMS'] as $arItem): ?>
        <?php
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <li id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
            <div><?= $arItem['NAME'] ?></div>
            <p>
                <i class="fa fa-quote-left" aria-hidden="true"></i>
                <?= strip_tags($arItem['PREVIEW_TEXT']) ?>
            </p>
        </li>
<? endforeach; ?>
    </ul>
</div>