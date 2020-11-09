<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

if (!empty($arResult['ITEMS']))
{

	$priceID = $GLOBALS['CURRENT_PRICE']['ID'];
	$priceName = $GLOBALS['CURRENT_PRICE']['NAME'];
?>

	<p class="h2">Новинки</p>
	<div class="row">

	<? foreach ($arResult['ITEMS'] as $arItem): ?>
		<?
		$collectImages = unserialize(stripslashes($arItem['PROPERTIES']['IMAGES']['~VALUE']));
		$collectImage = $collectImages[0];
		// llc($arResult['PAGINATION'] );
		?>
		<div class="col-md-6 col-lg-4 col-sm-6 col-xs-6 col-xs-sm" style="padding-right: 10px;padding-left: 0px;padding-bottom: 0px;padding-top: 0px;">

				<? if (($arItem['PROPERTIES']['NEW_K']['VALUE']) || ($arItem['PROPERTIES']['PROMO']['VALUE'])): ?>
					<div class="fr">
						<? if ($arItem['PROPERTIES']['NEW_K']['VALUE']): ?>
								<span class="pv2 ph3 mr2 bg-dark-red white b">НОВИНКА</span>
						<? endif; ?>
						<? if ($arItem['PROPERTIES']['PROMO']['VALUE']): ?>
								<span class="pv2 ph3 mr2 bg-gold white b">АКЦИЯ</span>
						<? endif; ?>
					</div>
				<? endif; ?>
				<div class="thumbnail mini" style="margin-bottom: 10px;">
					<a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="item-img">
													<span class="ttu"><?= $arItem['NAME'] ?></span>
													<img src="<?= $arItem['PREVIEW_PICTURE_CACHE']['src'] ?: '/upload/no-image.png' ?>" class="img-responsive" alt="<?= $arItem['NAME'] ?>" title="<?= $arItem['NAME'] ?>">
											</a>
					<div class="mt4 row category-bot">
					<div class="col-xs-6">
						<? if ($arItem['PROPERTIES']["MIN_PRICE_COLLECTION_$priceName"]['VALUE']): ?>
						от <strong><?= $arItem['PROPERTIES']["MIN_PRICE_COLLECTION_$priceName"]['VALUE']; ?></strong> р
						<? endif; ?>
					</div>
						<div class="col-xs-6 text-right"><a href="<?= $arItem['DETAIL_PAGE_URL'] ?>"> <i class="fa fa-chevron-right" aria-hidden="true"></i> <span>подробнее</span></a></div>
					</div>
				</div>
			</div>

		<? $prev_manufacturer = $arItem['PROPERTIES']['MANUFACTURER']['VALUE']; ?>
	<? endforeach; ?>
	<?
		}
	?>
	</div>
<div class="db">
<?=$arResult['PAGINATION']?>
</div>
